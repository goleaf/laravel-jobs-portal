<section class="pt40 pb80" id="job-post">
    <div class="container mx-auto px-4 mx-auto">
        <div class="md:w-full col-sm-12 flex-1 -xs-12 mb20">
            <h2 class="capitalize text-center">{{ __('web.home_menu.notices')  }}</h2>
        </div>
        <div class="flex-1 -lg-8 offset-lg-2 marquee- container mx-auto px-4 mx-auto">
            <div style="height: 360px;">
            <marquee direction="down" scrolldelay="200" id="notices">
                @foreach($notices as $notice)
                    <div class="flex flex-wrap line-break">
                        <div class="md:w-2/12 col-sm-2 flex-1 -xs-4">
                            <div class="notice_data">
                                <div class="event-date">
                                    {{ \Carbon\Carbon::parse($notice->created_at)->translatedFormat('jS M, Y')  }},
                                </div>
                            </div>
                        </div>
                        <div class="md:w-9/12 col-sm-9 flex-1 -xs-8">
                            <div class="ml-5">
                                <span class="font-weight-bold ml-4">{{ html_entity_decode($notice->title)  }} | {{ $notice->created_at->diffForHumans()  }}<br></span>
                            </div>
                            {!! nl2br(strip_tags($notice->description)) !!}
                        </div>
                    </div>
                    <br>
                    <br>
                @endforeach
            </marquee>
            </div>
        </div>
    </div>
</section>
