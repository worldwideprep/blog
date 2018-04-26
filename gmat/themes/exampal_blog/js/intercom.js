// Load Intercom earlier

if (window.intercomSettings !== undefined) {
    var intercomScript = document.createElement('script');
    intercomScript.type = 'text/javascript';
    intercomScript.async = true;
    intercomScript.src = 'https://widget.intercom.io/widget/'+window.intercomSettings.app_id;
    var intercomElem = document.getElementsByTagName('script')[0];
    intercomElem.parentNode.insertBefore(intercomScript, intercomElem);
}

