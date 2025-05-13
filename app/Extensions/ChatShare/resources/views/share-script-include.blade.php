<script>
    function fetchLink() {
        const categoryId = this.categoryId;
        const chatId = this.chatId;

        fetch('/share/link', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // CSRF token ekleniyor
            },
            body: JSON.stringify({
                category_id: categoryId,
                chat_id: chatId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.link) {
                    this.result = data.link;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function copyToClipboard($result) {
        const copyText = $result;

        if (copyText === undefined || copyText === null || copyText === ""){
            toastr.error('Please Generate Link')
        }else {
            navigator.clipboard.writeText(copyText).then(function() {
                toastr.success('Link copied to clipboard')
            }, function(err) {
                console.error('Failed to copy text: ', err);
            });
        }

    }

</script>