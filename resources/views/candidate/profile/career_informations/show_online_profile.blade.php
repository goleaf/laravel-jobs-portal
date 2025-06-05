<div class="flex-1">
    @if(isset($user->facebook_url))
        <a class="" href="{{ addLinkHttpUrl($user->facebook_url) }}"
           target="_blank" id="facebook_url"><span class="border border-gray-300 bg-transparent"><i
                        class="text-indigo-600 fab fa-facebook-f facebook-fa-icon -600"></i></span></a>
    @endif
    @if(isset($user->twitter_url))
        <a class="" href="{{ addLinkHttpUrl($user->twitter_url) }}"
           target="_blank" id="twitter_url"><span class="border border-gray-300 bg-transparent"><i
                        class="text-indigo-600 fab fa-twitter twitter-fa-icon -600"></i></span></a>
    @endif
    @if(isset($user->linkedin_url))
        <a class="" href="{{ addLinkHttpUrl($user->linkedin_url) }}"
           target="_blank" id="linkedin_url"><span class="border border-gray-300 bg-transparent"><i
                        class="text-indigo-600 fab fa-linkedin-in linkedin-fa-icon -600"></i></span></a>
    @endif
    @if(isset($user->google_plus_url))
        <a class="" href="{{ addLinkHttpUrl($user->google_plus_url) }}"
           target="_blank" id="google_plus_url"><span class="border border-gray-300 bg-transparent"><i
                        class="fab fa-google-plus-g google-plus-fa-icon text-red-600"></i></span></a>
    @endif
    @if(isset($user->pinterest_url))
        <a class="" href="{{ addLinkHttpUrl($user->pinterest_url) }}"
           target="_blank" id="pinterest_url"><span class="border border-gray-300 bg-transparent"><i
                        class="fab fa-pinterest-p pinterest-fa-icon text-red-600"></i></span></a>
    @endif
</div>

