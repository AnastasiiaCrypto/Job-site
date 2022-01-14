<?php
/**
 * JobClass - Geolocalized Job Board Script
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Account;

use App\Helpers\Arr;
use App\Helpers\Search;
use App\Http\Controllers\Search\Traits\PreSearchTrait;
use App\Models\Post;
use App\Models\Category;
use App\Models\SavedPost;
use App\Models\SavedSearch;
use App\Models\Scopes\ReviewedScope;
use App\Mail\PostDeleted;
use App\Models\Scopes\VerifiedScope;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Torann\LaravelMetaTags\Facades\MetaTag;

class PostsController extends AccountBaseController
{
    use PreSearchTrait;

    private $perPage = 12;

    public function __construct()
    {
        parent::__construct();

        $this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;
    }

    /**
     * @param $pagePath
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function getPage($pagePath)
    {
        view()->share('pagePath', $pagePath);

        switch($pagePath) {
            case 'my-posts':
                return $this->getMyPosts();
                break;
            case 'archived':
                return $this->getArchivedPosts($pagePath);
                break;
            case 'favourite':
                return $this->getFavouritePosts();
                break;
            case 'pending-approval':
                return $this->getPendingApprovalPosts();
                break;
            default:
                abort(404);
        }
    }

    /**
     * @return View
     */
    public function getMyPosts()
    {
        $data = [];
        $data['posts'] = $this->myPosts->paginate($this->perPage);
        $data['type'] = 'my-posts';

        // Meta Tags
        MetaTag::set('title', t('My ads'));
        MetaTag::set('description', t('My ads on :app_name', ['app_name' => config('settings.app.app_name')]));

        return view('account.posts', $data);
    }

    /**
     * @param $pagePath
     * @param null $postId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function getArchivedPosts($pagePath, $postId = null)
    {
        // If repost
		if (str_contains(url()->current(), $pagePath . '/' . $postId . '/repost')) {
            $res = false;
            if (is_numeric($postId) and $postId > 0) {
                $res = Post::find($postId)->update([
                    'archived'   => 0,
                    'created_at' => Carbon::now(),
                ]);
            }
            if (!$res) {
                flash(t("The repost has done successfully."))->success();
            } else {
                flash(t("The repost has failed. Please try again."))->error();
            }

            return redirect(config('app.locale') . '/account/' . $pagePath);
        }

        $data = [];
        $data['posts'] = $this->archivedPosts->paginate($this->perPage);

        // Meta Tags
        MetaTag::set('title', t('My archived ads'));
        MetaTag::set('description', t('My archived ads on :app_name', ['app_name' => config('settings.app.app_name')]));

        view()->share('pagePath', $pagePath);

        return view('account.posts', $data);
    }

    /**
     * @return View
     */
    public function getFavouritePosts()
    {
        $data = [];
        $data['posts'] = $this->favouritePosts->paginate($this->perPage);

        // Meta Tags
        MetaTag::set('title', t('My favourite jobs'));
        MetaTag::set('description', t('My favourite jobs on :app_name', ['app_name' => config('settings.app.app_name')]));

        return view('account.posts', $data);
    }

    /**
     * @return View
     */
    public function getPendingApprovalPosts()
    {
        $data = [];
        $data['posts'] = $this->pendingPosts->paginate($this->perPage);

        // Meta Tags
        MetaTag::set('title', t('My pending approval ads'));
        MetaTag::set('description', t('My pending approval ads on :app_name', ['app_name' => config('settings.app.app_name')]));

        return view('account.posts', $data);
    }

    /**
     * @param HttpRequest $request
     * @return View
     */
    public function getSavedSearch(HttpRequest $request)
    {
        $data = [];

        // Get QueryString
        $tmp = parse_url(url(Request::getRequestUri()));
        $queryString = (isset($tmp['query']) ? $tmp['query'] : 'false');
        $queryString = preg_replace('|\&pag[^=]*=[0-9]*|i', '', $queryString);

        // CATEGORIES COLLECTION
        $cats = Category::trans()->orderBy('lft')->get();
        $cats = collect($cats)->keyBy('translation_of');
        view()->share('cats', $cats);

        // Search
        $savedSearch = SavedSearch::currentCountry()
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->simplePaginate($this->perPage, ['*'], 'pag');

        if (collect($savedSearch->getCollection())->keyBy('query')->keys()->contains($queryString))
        {
            parse_str($queryString, $queryArray);

            // QueryString vars
            $cityId = isset($queryArray['l']) ? $queryArray['l'] : null;
            $location = isset($queryArray['location']) ? $queryArray['location'] : null;
            $adminName = (isset($queryArray['r']) && !isset($queryArray['l'])) ? $queryArray['r'] : null;

            // Pre-Search
            $preSearch = [
                'city'  => $this->getCity($cityId, $location),
                'admin' => $this->getAdmin($adminName),
            ];

            if ($savedSearch->getCollection()->count() > 0) {
                // Search
                $search = new Search($preSearch);
                $data = $search->fechAll();
            }
        }
        $data['savedSearch'] = $savedSearch;

        // Meta Tags
        MetaTag::set('title', t('My saved search'));
        MetaTag::set('description', t('My saved search on :app_name', ['app_name' => config('settings.app.app_name')]));

        view()->share('pagePath', 'saved-search');

        return view('account.saved-search', $data);
    }
	
	/**
	 * @param $pagePath
	 * @param null $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function destroy($pagePath, $id = null)
    {
        // Get Entries ID
        $ids = [];
        if (request()->filled('entries')) {
            $ids = request()->input('entries');
        } else {
            if (!is_numeric($id) && $id <= 0) {
                $ids = [];
            } else {
                $ids[] = $id;
            }
        }

        // Delete
        $nb = 0;
        if ($pagePath == 'favourite') {
			$savedPosts = SavedPost::where('user_id', auth()->user()->id)->whereIn('post_id', $ids);
            if ($savedPosts->count() > 0) {
                $nb = $savedPosts->delete();
            }
        } elseif ($pagePath == 'saved-search') {
            $nb = SavedSearch::destroy($ids);
        } else {
            foreach($ids as $item) {
                $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($item);
                if (!empty($post)) {
					$tmpPost = Arr::toObject($post->toArray());

                    // Delete Entry
                    $nb = $post->delete();

                    // Send an Email confirmation
					if (!empty($tmpPost->email)) {
						try {
							Mail::send(new PostDeleted($tmpPost));
						} catch (\Exception $e) {
							flash($e->getMessage())->error();
						}
					}
                }
            }
        }

        // Confirmation
        if ($nb == 0) {
            flash(t("No deletion is done. Please try again."))->error();
        } else {
            $count = count($ids);
            if ($count > 1) {
                $message = t("x :entities has been deleted successfully.", ['entities' => t('ads'), 'count' => $count]);
            } else {
                $message = t("1 :entity has been deleted successfully.", ['entity' => t('ad')]);
            }
            flash($message)->success();
        }

        return redirect(config('app.locale') . '/account/' . $pagePath);
    }
}
