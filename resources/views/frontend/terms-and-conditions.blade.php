@extends('frontend.layouts.default')
@section('title',"Around Airports - Terms and Conditions")
@section('description',"Making travel easier and more enjoyable by quickly helping you find locations of interest around major as well as regional airports in the United States and around the world.")
@section('keywords', 'around airports, airports')
@section('content')
    <div class="container mt-4">
        <div class="media">
            <div class="media-body">
                <h3 class="mt-0 mb-1">Terms and Conditions</h3>
                <hr>
                <p>
                    Welcome to Around Airports. If you continue to browse and use this website/app you are agreeing to comply with and be bound by the following terms and conditions of use, which together with our <a href="{{route('privacy.index')}}">privacy policy</a> govern our relationship with you in relation to this website and/or our mobile apps.
                </p>
                <p>
                    The term ‘us’ or ‘we’ refers to the owner of the website/apps. The term ‘you’ refers to the user or viewer of our website and/or mobile apps.
                </p>

                <h4 class="mt-0 mb-2">Terms of Use <a href="config('services.twitter_url)" target="_blank">@aroundairports</a></h4>
                <p>
                    The use of this website and our mobile apps is subject to the following terms of use:
                </p>

                <ul>
                    <li>The content of the pages of this website and views within our mobile apps are for your general information and use only. It is subject to change without notice.</li>
                    <li>
                        Neither we nor any third parties provide any warranty or guarantee as to the accuracy, timeliness, performance, completeness or suitability of the information and materials found or offered on this website or within our mobile apps for any particular purpose. You acknowledge that such information and materials may contain inaccuracies or errors and we expressly exclude liability for any such inaccuracies or errors to the fullest extent permitted by law.
                    </li>
                    <li>
                        Your use of any information or materials on this website or within our mobile apps is entirely at your own risk, for which we shall not be liable. It shall be your own responsibility to ensure that any products, services or information available through this website or our mobile apps meet your specific requirements.
                    </li>
                    <li>
                        This website and our mobile apps contains material which is owned by or licensed to us. This material includes, but is not limited to, the design, layout, look, appearance and graphics. Reproduction is prohibited other than in accordance with the copyright notice, which forms part of these terms and conditions.
                    </li>
                    <li>All trademarks reproduced in this website and our mobile apps which are not the property of, or licensed to, the operator are acknowledged on the website or within our mobile apps.</li>
                    <li>Unauthorized use of this website or our mobile apps may give rise to a claim for damages and/or be a criminal offense.</li>
                    <li>
                        From time to time this website or our mobile apps may also include links to other websites or mobile applications. These links are provided for your convenience to provide further information. They do not signify that we endorse the website(s). We have no responsibility for the content of the linked website(s).
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
