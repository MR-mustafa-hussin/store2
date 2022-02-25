jQuery(function() {
    var $activationSection = jQuery("#trustedsite-activation");
    var $dashboardSection = jQuery("#trustedsite-dashboard");
    var $upgradeSection = jQuery("#trustedsite-upgrade");
    var $exceedSection = jQuery("#trustedsite-exceed");
    var $loadSection = jQuery("#trustedsite-load");
    var $errorSection = jQuery("#trustedsite-error");
    var $conversionTrackingSection = jQuery("#setup-conversion-tracking");
    
    var $data = jQuery("#trustedsite-data");

    var host = $data.attr('data-host');
    if (!host) { host = ''; }
    if (host.startsWith("www.")) { host = host.substr(4); }
    
    var email = $data.attr('data-email');
    if (!email) { email = ''; }

    var affiliate = 221269;

    var apiBase = 'https://cdn.trustedsite.com';
    var apiUrl = apiBase + '/api/v2/site-lookup.json?host=' + encodeURIComponent(host);
    
    var websiteUrl = 'https://www.trustedsite.com';
    var urlBase = websiteUrl + '/user/site/' + host;
    
    var exceedUrl = urlBase + '/upgrade';
    
    var certSecureUrl = urlBase + '/cert/secure';
    var certVerifiedBusinessUrl = urlBase + '/cert/business';
    var certIssueFreeOrdersUrl = urlBase + '/cert/ifo';
    var certShopperIdentityProtectionUrl = urlBase + '/cert/sip';
    var certSpamFreeUrl = urlBase + '/cert/inbox';
    var certDataProtectionUrl = urlBase + '/cert/ssl';
    var certTrustedReviewsUrl = urlBase + '/cert/reviews';
    var certSecureCloudUrl = urlBase + '/cert/vuln';
    
    var tmFloatingUrl = urlBase + '/tm/floating';
    var tmEngagementUrl = urlBase + '/tm/engagement';
    var tmShopperIdentityProtectionUrl = urlBase + '/tm/sip';
    var tmTestimonialsUrl = urlBase + '/tm/testimonials';
    var tmBannerUrl = urlBase + '/tm/banner';
    
    var setUpMainCodeUrl = urlBase + '/setup/main';
    var setUpConversionTrackingUrl = urlBase + '/setup/conversion';
    var setUpDirectoryUrl = urlBase + '/setup/directory';
    
    var addOnSearchSubmissionUrl = urlBase + '/sitemap/';
    var addOnDiagnosticsUrl = urlBase + '/diagnostics';
    var addOnBreachInsuranceUrl = urlBase + '/add?product=8';

    jQuery("#activate-now").click(function() {
        var email_input = jQuery('#email-input').val();
        var domain_input = jQuery('#domain-input').val();
        var signupUrl = websiteUrl + "/signup?re=" + encodeURIComponent(domain_input)
                                            + "&email=" + encodeURIComponent(email_input) 
                                            + "&affId=" + encodeURIComponent(affiliate)
                                            + "&utm_source=wordpress";
        var signupWindow = window.open(signupUrl);
    });

    function setLinkText($el, linkText) {
        $el.find(".link").html(linkText);
    }

    function setLinkHref($el, href) {
        var link = "<a href=" + href + " target=\"_blank\" style=\"text-decoration:none\"></a>"
        $el.wrapAll(link);
    }

    function checkIcon($el) {
        $el.find('.status-icon').html('<i class="fa fa-check-circle"></i>');
    }

    function warningIcon($el) {
        $el.find('.status-icon').html('<i class="fa fa-warning"></i>');
    }

    function circleIcon($el) {
        $el.find('.status-icon').html('<i class="fa fa-circle-thin"></i>');
    }

    function lockIcon($el) {
        $el.find('.status-icon').html('<i class="fa fa-lock"></i>');
    }

    function updateCert($row, status) {
        if (status == 'upgrade') {
            setLinkText($row, "Upgrade");
            lockIcon($row);
        } else if (status == 'pass') {
            setLinkText($row, 'Certified');
            checkIcon($row);
        } else if (status == 'fail') {
            setLinkText($row, "Not Certified");
            circleIcon($row);
        } else if (status == 'exceed') {
            setLinkText($row, 'Visit Limit Exceeded');
            warningIcon($row);
        } else {
            circleIcon($row);
        }
    }
    
    function renderCertSecure(data) {
        $row = jQuery('#certified-secure');
        var status;
        if (data['lite'] && data['lite']['visit_limit_exceeded'] == 1) status = 'exceed';
        else status = data['certs']['secure']['status'];
        updateCert($row, status);
        setLinkHref($row, certSecureUrl);
    }
    
    function renderCertVerifiedBusiness(data) {
        $row = jQuery('#verified-business');
        var status;
        if (data['lite'] && data['lite']['visit_limit_exceeded'] == 1) status = 'exceed';
        else status = data['certs']['business']['status'];
        updateCert($row, status);
        setLinkHref($row, certVerifiedBusinessUrl);
    }
    
    function renderCertIssueFreeOrders(data) {
        $row = jQuery('#issue-free-orders');
        var status = data['certs']['ifo']['status'];
        updateCert($row, status);
        setLinkHref($row, certIssueFreeOrdersUrl);
    }
    
    function renderCertShopperIdentityProtection(data) {
        $row = jQuery('#shopper-identity-protection');
        var status = data['certs']['sip']['status'];
        updateCert($row, status);
        setLinkHref($row, certShopperIdentityProtectionUrl);
    }
    
    function renderCertSpamFree(data) {
        $row = jQuery('#spam-free');
        var status = data['certs']['inbox']['status'];
        updateCert($row, status);
        setLinkHref($row, certSpamFreeUrl);
    }
    
    function renderCertDataProtection(data) {
        $row = jQuery('#data-protection');
        var status = data['certs']['ssl']['status'];
        updateCert($row, status);
        setLinkHref($row, certDataProtectionUrl);
    }
    
    function renderCertTrustedReviews(data) {
        $row = jQuery('#trusted-reviews');
        var status = data['certs']['reviews']['status'];
        updateCert($row, status);
        setLinkHref($row, certTrustedReviewsUrl);
    }
    
    function renderCertSecureCloud(data) {
        $row = jQuery('#secure-cloud');
        var status = data['certs']['vuln']['status'];
        updateCert($row, status);
        setLinkHref($row, certSecureCloudUrl);
    }
    
    function renderCert(data) {
        renderCertSecure(data);
        renderCertVerifiedBusiness(data);
        renderCertIssueFreeOrders(data);
        renderCertShopperIdentityProtection(data);
        renderCertSpamFree(data);
        renderCertDataProtection(data);
        renderCertTrustedReviews(data);
        renderCertSecureCloud(data);
    }
    
    function updateTrustmark($row, status) {
        if (status == 'upgrade') {
            lockIcon($row);
        } else if (status == 'active') {
            checkIcon($row);
        } else if (status == 'not-active') {
            circleIcon($row);
        } else if (status == 'exceed') {
            warningIcon($row);
        } else {
            circleIcon($row);
        }
    }
    
    function renderTrustmarkFloating(data) {
        $row = jQuery('#floating-tm');
        var status;
        if (data['lite'] && data['lite']['visit_limit_exceeded'] == 1) status = 'exceed';
        else status = data['tms']['float']['status'];
        updateTrustmark($row, status);
        setLinkHref($row, tmFloatingUrl);
    }
    
    function renderTrustmarkEngagement(data) {
        $row = jQuery('#engagement-tm');
        var status = data['tms']['engagement']['status'];
        updateTrustmark($row, status);
        setLinkHref($row, tmEngagementUrl);
    }
    
    function renderTrustmarkShopperIdentityProtection(data) {
        $row = jQuery('#shopper-identity-protection-tm');
        var status = data['tms']['sip']['status'];
        updateTrustmark($row, status);
        setLinkHref($row, tmShopperIdentityProtectionUrl);
    }
    
    function renderTrustmarkTestimonials(data) {
        $row = jQuery('#testimonials-tm');
        var status = data['tms']['testimonials']['status'];
        updateTrustmark($row, status);
        setLinkHref($row, tmTestimonialsUrl);
    }
    
    function renderTrustmarkBanner(data) {
        $row = jQuery('#banner-tm');
        var status = data['tms']['banner']['status'];
        updateTrustmark($row, status);
        setLinkHref($row, tmBannerUrl);
    }
    
    function renderTrustmarks(data) {
        renderTrustmarkFloating(data);
        renderTrustmarkEngagement(data);
        renderTrustmarkShopperIdentityProtection(data);
        renderTrustmarkTestimonials(data);
        renderTrustmarkBanner(data);
    }
    
    function renderSetupMainCode() {
        $row = jQuery('#setup-main-code');
        checkIcon($row);
        var strike = "<s></s>"
        $row.wrapInner(strike);
        setLinkHref($row, setUpMainCodeUrl);
        $row.attr('title', 'This script has been automatically installed by our plugin. The TrustedSite portal will reflect this within a few minutes of the script detecting traffic from your site.');
    }
    
    function renderSetupConversionTracking() {
        $row = jQuery('#setup-conversion-tracking');
        checkIcon($row);
        var strike = "<s></s>"
        $row.wrapInner(strike);
        setLinkHref($row, setUpConversionTrackingUrl);
        $row.attr('title', 'This script has been automatically installed by our plugin for use with WooCommerce. The TrustedSite portal will reflect this within a few minutes of the script detecting a conversion from your site.');
    }
    
    function renderSetupDirectory(data) {
        $row = jQuery('#setup-directory-listing');
        var status = data['directory']['complete'];
        if (status == '1') {
            var strike = "<s></s>"
            $row.wrapInner(strike);
            checkIcon($row);
        } else if (status == '0') {
            circleIcon($row);
        } else {
            circleIcon($row);
        }
        setLinkHref($row, setUpDirectoryUrl);
    }
    
    function renderSetup(data) {
        renderSetupMainCode();
        renderSetupConversionTracking();
        renderSetupDirectory(data);
    }
    
    function renderUsage(data) {
        if (data['pro'] == 0 && data['lite']) {
            $meter = jQuery('#usage-meter');
            $text = jQuery('#usage-text');
            
            var limit = data['lite']['visit_limit'];
            var current = data['lite']['visit_count'];
            var exceeded = data['lite']['visit_limit_exceeded'];
            
            if(exceeded == '1') {
                $text.html('<span class="status-icon"></span>You\'ve exceeded your monthly limit. To continue displaying the trustmark, <a class="blue-link" href="' + exceedUrl + '">upgrade today</a>.');
                $text.css("text-align", "center");
            } else if (exceeded == '0') {
                $meter.attr({
                    "max" : limit,
                    "value" : current
                });
                
                $text.html('<span class="status-icon"></span>' + current + "/" + limit + " visits used this month.");
            }
        }
    }
    
    function sizeVideo() {
        var video = Wistia.api('h04o4ou8tz');
        video.videoWidth(jQuery('#ts-video').parent().width(), {constrain:true});
        jQuery('#ts-video').height(video.videoHeight());
    }
    
    function renderUpgrade(data) {
        renderUsage(data);
        window._wq = window._wq || [];
        _wq.push({ id: 'h04o4ou8tz', onReady: function(video) {
            sizeVideo();
            window.addEventListener('resize', sizeVideo);
        }});
    }
    
    function updateAddon($row, status) {
        if (status == 'active') {
            setLinkText($row, 'Active');
            checkIcon($row);
        } else {
            circleIcon($row);
        }
    }
    
    function renderAddOnSearchSubmission(data) {
        $row = jQuery('#addons-search-submission');
        var status = data['sitemap']['status'];
        updateAddon($row, status);
        setLinkHref($row, addOnSearchSubmissionUrl);
    }
    
    function renderAddOnDiagnostics(data) {
        $row = jQuery('#addons-diagnostics');
        var status = data['diagnostics']['status'];
        updateAddon($row, status);
        setLinkHref($row, addOnDiagnosticsUrl);
    }
    
    function renderAddOnBreachInsurance(data) {
        $row = jQuery('#addons-breach-insurance');
        var status = data['breach_insurance']['status'];
        updateAddon($row, status);
        setLinkHref($row, addOnBreachInsuranceUrl);
    }
    
    function renderAddOns(data) {
        renderAddOnSearchSubmission(data);
        renderAddOnDiagnostics(data);
        renderAddOnBreachInsurance(data);
    }
    
    function refresh() {
        jQuery.getJSON(apiUrl + "&cache=" + new Date().getMinutes(), function(data) {
            $loadSection.hide();
            var status = data['success'];
            if (status == '0') {
                $activationSection.show();
                $dashboardSection.hide();
            } else {
                $activationSection.hide();
                $dashboardSection.show();
                clearInterval(refreshInterval);
                loadDashboard(data);
            }
        })
        .fail(function () {
            refreshTimes++;
            if(refreshTimes > 24) {
                clearInterval(refreshInterval);
                $loadSection.hide();
                $errorSection.show();
            }
        });
    }
    
    function loadDashboard(data) {
        renderCert(data);
        renderTrustmarks(data);
        renderSetup(data);
        renderUpgrade(data);
        renderAddOns(data);

        if (data['pro'] != 1) {
            renderUpgrade(data);
            $upgradeSection.show();
        } else {
            $conversionTrackingSection.show();
        }
        if (data['lite'] && data['lite']['visit_limit_exceeded'] == 1) {
            setLinkHref($exceedSection, exceedUrl);
            $exceedSection.show();
        }
        $dashboardSection.show();
    }
    
    $loadSection.show();
    var refreshInterval = setInterval(refresh, 5000);
    var refreshTimes = 0;
    refresh();
});
