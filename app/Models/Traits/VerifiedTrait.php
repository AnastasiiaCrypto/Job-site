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

namespace App\Models\Traits;


trait VerifiedTrait
{
    public function getVerifiedEmailHtml()
    {
        if (!isset($this->verified_email)) return false;
        
        // Get checkbox
        $out = ajaxCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'verified_email', $this->verified_email);
        
        // Get all entity's data
        $entity = self::find($this->{$this->primaryKey});
        
        if (!empty($entity->email)) {
            if ($entity->verified_email != 1) {
            	// ToolTip
				$toolTip = (!empty($entity->email)) ? 'data-toggle="tooltip" title="' . trans('admin::messages.To') . ': ' . $entity->email . '"' : '';
				
                // Show re-send verification message link
                $entitySlug = ($this->getTable() == 'users') ? 'user' : 'post';
                $urlPath = 'verify/' . $entitySlug . '/' . $this->{$this->primaryKey} . '/resend/email';
				$actionUrl = admin_url($urlPath);
    
				// HTML Link
                $out .= ' &nbsp;';
				$out .= '<a class="btn btn-default btn-xs" href="' . $actionUrl . '" ' . $toolTip . '>';
				$out .= '<i class="fa fa-send-o"></i> ';
				$out .= trans('admin::messages.Re-send link');
				$out .= '</a>';
            } else {
                // Get social icon (if exists)
                if ($this->getTable() == 'users') {
                    if (!empty($entity) && isset($entity->provider)) {
                        if (!empty($entity->provider)) {
                            if ($entity->provider == 'facebook') {
                                $toolTip = 'data-toggle="tooltip" title="' . trans('admin::messages.Registered with :provider', ['provider' => 'Facebook']) . '"';
                                $out .= ' &nbsp;<i class="admin-single-icon fa fa-facebook-square" style="color: #3b5998;" ' . $toolTip . '></i>';
                            }
                            if ($entity->provider == 'google') {
                                $toolTip = 'data-toggle="tooltip" title="' . trans('admin::messages.Registered with :provider', ['provider' => 'Google']) . '"';
                                $out .= ' &nbsp;<i class="admin-single-icon fa fa-google-plus-square" style="color: #d34836;" ' . $toolTip . '></i>';
                            }
                        }
                    }
                }
            }
        } else {
            $out = checkboxDisplay($this->verified_email);
        }
        
        return $out;
    }
    
    public function getVerifiedPhoneHtml()
    {
        if (!isset($this->verified_phone)) return false;
        
        // Get checkbox
        $out = ajaxCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'verified_phone', $this->verified_phone);
        
        // Get all entity's data
        $entity = self::find($this->{$this->primaryKey});
        
        if (!empty($entity->phone)) {
            if ($entity->verified_phone != 1) {
            	// ToolTip
				$toolTip = (!empty($entity->phone)) ? 'data-toggle="tooltip" title="' . trans('admin::messages.To') . ': ' . $entity->phone . '"' : '';
				
                // Show re-send verification message code
                $entitySlug = ($this->getTable() == 'users') ? 'user' : 'post';
                $urlPath = 'verify/' . $entitySlug . '/' . $this->{$this->primaryKey} . '/resend/sms';
				$actionUrl = admin_url($urlPath);
                
                // HTML Link
                $out .= ' &nbsp;';
				$out .= '<a class="btn btn-default btn-xs" href="' . $actionUrl . '" ' . $toolTip . '>';
				$out .= '<i class="fa fa-mobile"></i> ';
				$out .= trans('admin::messages.Re-send code');
				$out .= '</a>';
            }
        } else {
            $out = checkboxDisplay($this->verified_phone);
        }
        
        return $out;
    }
}