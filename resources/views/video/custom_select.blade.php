<x-adminlte-select2 name="edit_album_id" igroup-size="sm">
    <option value="0">全部</option>
    @foreach($value['option'] as $videoAlbumDataObj)
        <option
            value="{{$videoAlbumDataObj->id}}" >{{$videoAlbumDataObj->title}}</option>
    @endforeach
</x-adminlte-select2>