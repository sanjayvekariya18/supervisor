<div class="col-xl-3 col-lg-4 col-md-4 col-xs-12">
    <!-- Social timeline left start -->
    <div class="social-timeline-left">
        <!-- social-profile card start -->
        <div class="card">
            <div class="social-profile">
                <img class="img-fluid width-100" src="/images/faq_man.png" alt="">
                {{-- <div class="profile-hvr m-t-15"> --}}
                    {{-- <i class="icofont icofont-ui-edit p-r-10"></i> --}}
                    {{-- <i class="icofont icofont-ui-delete"></i> --}}
                {{-- </div> --}}
            </div>
            <div class="card-block social-follower">
                <h4>{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</h4>
                <h4>ID : {{ Auth::user()->id }}</h4>
                <h5>{{ Auth::user()->contact_number }}</h5>
                
            </div>
        </div>
        <!-- social-profile card end -->
    </div>
    <!-- Social timeline left end -->
</div>