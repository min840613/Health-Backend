<!-- Modal Start-->
<div class="modal fade" id="embedModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="gallery-modal-header">
                <button type="button" class="close mr-4 mt-4" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="container-fluid">
                        <div class="col-md-12 row">
                            <label class="col-sm-2 col-form-label">輸入關鍵字</label>
                            <div class="col-sm-8">
                                <input id="ModalKeywords" class="form-control" name="keywords" placeholder="請輸入關鍵字（使用『,』分隔）">
                                <input id="NowJson" class="form-control d-none" placeholder="">
                            </div>
                            <div class="col-sm-2">
                                <a class="btn btn-success btn-flat float-left px-4 searchArticle" href="javascript:;">搜尋</a>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <table id="extend_data" class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">選擇</th>
                                        <th scope="col">文章ID</th>
                                        <th scope="col">文章標題</th>
                                        <th scope="col">發佈時間</th>
                                    </tr>
                                </thead>
                                <tbody id="searchArticleTable"></tbody>
                            </table>
                            <br>
                            <nav class="embedNavigation" aria-label="Page navigation">
                                <ul class="pagination">
                                    
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="sendEmbedCheck" type="button" class="btn btn-primary">送出</button>
                <button type="button" class="btn btn-secondary closeModal" data-dismiss="modal">關閉</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal End-->