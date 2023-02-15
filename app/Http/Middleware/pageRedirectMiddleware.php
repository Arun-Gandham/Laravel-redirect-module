<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\pageRedirectModal;

class pageRedirectMiddleware
{
    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUrl = request()->path();
        $pageRedirectUrls = pageRedirectModal::getRedirectUrlsFromCurrentUrl($currentUrl);
        if(isset($pageRedirectUrls[0]))
        {
            $redirectUrl = "/";
            foreach($pageRedirectUrls as $eachUrl)
            {
                $redirectUrl = $eachUrl->to;
            }
            pageRedirectModal::incrementHitCount($eachUrl->id);
            return redirect($redirectUrl);
        }
        return $next($request);
    }
}
