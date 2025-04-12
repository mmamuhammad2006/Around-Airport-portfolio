<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/">
            <img
                src="{{asset('frontend/images/favicon.svg')}}"
                width="30"
                height="30"
                class="d-inline-block align-top"
                alt="Around Airports">
            Around Airports
        </a>
        @if(@$categories && @$categories->count() > 0)
            <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    @foreach($categories as $index => $category)
                        @if($index < 4)
                            <li class="nav-item active">
                                <a
                                    class="nav-link"
                                    href="{{route('airports.code.category', ['code' => $airport->code, 'category'=>$category->slug])}}">{{ $category->name }}</a>
                            </li>
                        @endif
                    @endforeach

                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="navbarDropdown"
                            role="button"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            More
                        </a>
                        <div
                            class="dropdown-menu"
                            aria-labelledby="navbarDropdown">
                            @foreach($categories as $index=>$category)
                                @if($index > 4)
                                    <a
                                        class="dropdown-item"
                                        key="{{$category->id}}"
                                        href="{{route('airports.code.category', ['code' => $airport->code, 'category'=>$category->slug])}}">{{ $category->name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </li>
                </ul>
            </div>
        @endif
    </nav>
    @if(@$categories && $categories->count() > 0)
        <div class="nav-scroller bg-white shadow-sm d-lg-none d-xl-none">
            <nav class="nav nav-underline">
                @foreach($categories as $index=> $category)
                    <a key="{{$category->id}}"
                       href="{{route('airports.code.category', ['code' => $airport->code, 'category'=>$category->slug])}}"
                       class="nav-link">
                        <i class="{{$category->icon}}"></i>&nbsp;&nbsp;{{ $category->name }}
                    </a>
                @endforeach
            </nav>
        </div>
    @endif

    <div class="container mt-4">
        <a href="https://www.liveryaccess.com">
            <img src="{{ asset('frontend/images/banners.gif') }}" class="d-block mx-auto" width="100%" height="100%">
        </a>
    </div>

</header>
