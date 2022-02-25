<?php
    defined('ABSPATH') OR exit;
    
    $email = get_option( 'admin_email' );
    $arrHost = parse_url(home_url('', $scheme = 'http'));
    $host = $arrHost['host'];
    
    $endpoint = "https://www.trustedsite.com";
    ?>

<div class="wrap" id="trustedsite-container">

<div id="trustedsite-data" data-host="<?php echo $host; ?>" data-email="<?php echo $email; ?>"></div>


<div id="trustedsite-load" class="lds-ring">
<div class="lds-ring"></div>
</div>

<div id="trustedsite-error">
<h1>TrustedSite</h1>
Sorry, we have encountered an error loading your TrustedSite dashboard. If you have just activated your account, please allow up to a few minutes and try again. Otherwise, feel free to contact <a href="https://support.trustedsite.com">TrustedSite Support</a>.
</div>

<div id="trustedsite-activation">
<h1>TrustedSite</h1>
<br/>
<div id="signup-header">Your Account</div>
<div id="signup-text">To activate the app, please create your TrustedSite account. </div>

<form>
<span id="email">Email
<input id="email-input" class="ts-input" type="text" name="email" value="<?php echo get_option('admin_email')?>"></span><br>
<span id="domain">Domain
<input id="domain-input" class="ts-input" type="text" name="domain" value="<?php echo get_option('siteurl')?>"></span><br><br>
<input type="button" value="Create Account" id="activate-now">
</form>
</div>

<div id="trustedsite-dashboard">
<h1>TrustedSite</h1>

<div class="row row-last row-txt highlight" id="trustedsite-exceed">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="ts-long"><i class="fa fa-warning"></i>  You've exceeded your monthly visit limit. Upgrade now to continue displaying TrustedSite trustmarks.
</div>
</div>

<div class="wrapper">

<div class="left">
<div class="content" id="certifications">


<div class="row row-txt ts-title">
<span class="status-icon"></span>
Certifications
</div>

<div class="row row-txt highlight" id="certified-secure">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Certified Secure
</div>
</div>

<div class="row row-txt highlight" id="verified-business">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Verified Business
</div>
</div>

<div class="row row-txt highlight" id="issue-free-orders">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Issue Free Orders
</div>
</div>

<div class="row row-txt highlight" id="shopper-identity-protection">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Shopper Identity Protection
</div>
</div>

<div class="row row-txt highlight" id="data-protection">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Data Protection
</div>
</div>

<div class="row row-txt highlight" id="spam-free">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Spam-Free
</div>
</div>

<div class="row row-txt highlight" id="trusted-reviews">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Trusted Reviews
</div>
</div>

<div class="row row-last row-txt highlight" id="secure-cloud">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Secure Cloud
</div>
</div>

</div>

<div class="content" id="trustmarks">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
Trustmarks
</div>

<div class="row row-img ts-img highlight" id="floating-tm">
<div>
<span class="status-icon"></span>
Floating
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo plugins_url('../images/preview-64-floating.png',__FILE__)?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

<div class="row row-img ts-img highlight" id="engagement-tm">
<div>
<span class="status-icon"></span>
Engagement
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo plugins_url('../images/preview-64-engagement.png',__FILE__)?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

<div class="row row-img ts-img highlight" id="shopper-identity-protection-tm">
<div>
<span class="status-icon"></span>
Shopper Identity Protection
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo plugins_url('../images/preview-64-sip.png',__FILE__)?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

<div class="row row-img ts-img highlight" id="testimonials-tm">
<div>
<span class="status-icon"></span>
Testimonials
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo plugins_url('../images/preview-64-testimonials.png',__FILE__)?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

<div class="row row-last row-img ts-img highlight" id="banner-tm">
<div>
<span class="status-icon"></span>
Banner
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo plugins_url('../images/preview-64-banner.png',__FILE__)?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

</div>
</div>

<div class="right">

<div class="content" id="setup">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
Set Up
</div>

<div class="row row-txt highlight" id="setup-main-code">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link"></div>
<div class="ts-row">
<span class="status-icon"></span>
Main Code Installed
</div>
</div>

<div class="row row-txt highlight" id="setup-conversion-tracking">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link"></div>
<div class="ts-row">
<span class="status-icon"></span>
Set Up Conversion Tracking
</div>
</div>

<div class="row row-txt highlight" id="setup-directory-listing">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link"></div>
<div class="ts-row">
<span class="status-icon"></span>
Complete Directory Listing
</div>
</div>

<div class="row row-last row-txt no-arrow">
<form action="<?php echo $endpoint ?>/user/site/<?php echo $host ?>/" method="get" target="_blank">
<button class="ts-button" type="submit">Manage Account</button>
</form>
</div>

</div>

<div class="content" id="trustedsite-upgrade">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
Upgrade to Pro
</div>

<div class="row no-arrow">
<script src="//fast.wistia.com/embed/medias/h04o4ou8tz.jsonp" async></script>
<script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
<div class="wistia_embed wistia_async_h04o4ou8tz" id="ts-video">&nbsp;</div>

</div>

<div class="row row-txt no-arrow" id="upgrade-link">
<div class="centered-text">
Get our full suite of trust-building tools and start boosting sales today.
</div>
<br>
<div>
<form action="<?php echo $endpoint ?>/user/site/<?php echo $host ?>/upgrade" method="get" target="_blank">
<button class="ts-button" type="submit">Upgrade Now</button>
</form>
</div>
</div>

<div class="row row-txt no-arrow" id="usage">
<div class="ts-title">
<span class="status-icon"></span>
Visit Usage
</div>
<progress id="usage-meter"></progress>
<div id="usage-text">
<span class="status-icon"></span>
</div>
</div>

</div>


<div class="content" id="addons">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
Add-Ons
</div>

<div class="row row-txt highlight" id="addons-search-submission">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">Learn More</div>
<div class="ts-row">
<span class="status-icon"></span>
Search Submission
</div>
</div>

<div class="row row-txt highlight" id="addons-diagnostics">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">Learn More</div>
<div class="ts-row">
<span class="status-icon"></span>
Diagnostics
</div>
</div>

<div class="row row-last row-txt highlight" id="addons-breach-insurance">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">Learn More</div>
<div class="ts-row">
<span class="status-icon"></span>
Breach Insurance
</div>
</div>

</div>

</div>
</div>
</div>
</div>
