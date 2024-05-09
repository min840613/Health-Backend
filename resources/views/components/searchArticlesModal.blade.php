<!-- 搜尋文章 Modal Start-->
<div class="modal fade" id="searchArticlesModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id='searchArticlesModalTitle' class="modal-title">文章</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<iframe class="searchArticles_iframe" width="100%" height="600" frameborder="0" allowfullscreen="" id="searchArticlesIframe" name="searchArticlesIframe" src="{{ route('articles.search') }}"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- 搜尋文章 Modal End-->
