<!-- Modal Start-->
<div class="modal fade" id="VideoGalleryModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
        	<div class="video-modal-header">
                <button type="button" class="close mt-4 mr-4" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
            	<iframe class="VideoGalleryIframe" width="100%" height="500" frameborder="0" allowfullscreen="" name="VideoGalleryIframe" src="{{ url('video-gallery') }}"></iframe>
            </div>

        </div>
    </div>
</div>
<!-- Modal End-->
