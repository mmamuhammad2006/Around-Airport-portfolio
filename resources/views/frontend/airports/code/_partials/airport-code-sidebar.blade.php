<div class="list-group">
    @foreach($categories as $index => $category)
        <a
            key="{{$category->id}}"
            href="{{route('airports.code.category', ['code' => $airport->code, 'category'=>$category->slug])}}"
            class="list-group-item list-group-item-action">
            <i class="{{$category->icon}}"></i>&nbsp;&nbsp;{{ $category->name }}
        </a>
    @endforeach
</div>
