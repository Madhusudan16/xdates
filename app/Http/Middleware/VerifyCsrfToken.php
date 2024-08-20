<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier; 

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = ['/policy/lines','/policy/industry','/policy/personal','/policy/commercial','/policy/delete','/user','/user/deactive','/user/active','/user/delete','/user/edit','/policy/confirm','admin/user-manager','/admin/deactive','/admin/active','/admin/delete','/admin/edit','/admin/user','/manage-plans','/admin/plan-create','/admin/plan-edit','/admin/plan-delete','/planbill/change-plan','/planbill/userPlan-change','/admin/plan/active','/admin/plan/deactive', '/admin/coupon-manage','/admin/coupon-create','/admin/coupon/deactive','admin/coupon/active','/admin/coupon-delete','/admin/coupon-edit', 
        // 
    ];
}
