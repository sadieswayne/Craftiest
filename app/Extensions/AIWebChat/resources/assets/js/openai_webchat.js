function startAnalyze(event) {
    const inputs = event.target.elements;
    const locale = inputs['locale'].value;
    const category_id = inputs['category_id'].value;
    const website_url = inputs['website_url'].value;


    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (error) {
            return false;
        }
    }

    if (!isValidUrl(website_url)) {
        toastr.warning('Please input valid URL.');
        return false;
    }

    var formData = new FormData();
    formData.append('category_id', category_id);
    formData.append('website_url', website_url);
    const analyzeBtn = document.querySelector('#start_analyze_btn');

    analyzeBtn.setAttribute('disabled', true);

    Alpine.store('appLoadingIndicator').show();

    const req = startNewChat(category_id, locale, website_url);

    req.always(() => {
        analyzeBtn.setAttribute('disabled', false);
        Alpine.store('appLoadingIndicator').hide();
    });
}