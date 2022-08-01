<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - Dashboard</title>

    <!-- Bootstrap 4.5.2 CSS -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/bootstrap/css/bootstrap.min.css')}}">

    <!-- Custom bootstrap datepicker CSS for bootstrap version 4 -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/datetimepicker/css/bootstrap-datetimepicker.min.css')}}">
    
    <!-- Sweetalert2 CSS 10.16.0 -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/sweetalert2/css/sweetalert2.min.css')}}">

    <!-- Select2 CSS 4.1.0 -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/select2/css/select2.min.css')}}">

    <!-- Font-awesome CSS 5.15.3 -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/font-awesome/css/all.min.css')}}" type="text/css">

    <!-- DataTable Bootstrap 4 CSS 1.10.24 -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/datatables/css/dataTables.bootstrap4.min.css')}}">

    <!-- DataTable Fixedheader CSS 3.1.8 -->
    

    <!-- Custom Bootstrap 4 Notification  -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/css/admin/bootstrap-notifications.min.css')}}">
    

    <link rel="stylesheet" href="{{asset ('temp/vendor/datatables/css/dataTables.bootstrap4.min.css')}}">

    <!-- Datatables responsive bootstrap CSS 2.2.7 -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/datatables/responsive/css/responsive.bootstrap.min.css')}}">
    
    <!-- Tagify CSS 4.1.1 -->
    <link rel="stylesheet" href="{{asset ('temp/vendor/tagify/css/tagify.min.css')}}">
    <!-- Custom styles for this template
    <link href="{{ asset('temp/vendor/css/admin/sb-admin.css') }}" rel="stylesheet">-->
    <link href="{{ asset('temp/vendor/css/admin/style.css') }}" rel="stylesheet">

    <!-- Full calendar CSS 5.8.0 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.8.0/main.min.css">
    
    <!-- ROLE CSS-->
    @yield('css_role_create_tags')
    @yield('css_role_edit_tags')

    <!-- MY ROUTES CSS-->
    @yield('css_my_route_create_tags')
    

  </head>

  <body id="page-top">
    <nav class="navbarGrid">
          
      <div class="navbar-brand">
        <a href="/">Web App</a>
      </div>
      
      <div class="navbarSidebarToggleBtn">
        <button class="btn btn-link" id="sidebarToggle" href="#">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <!-- Navbar Search -->
      <div class="navbarSearch">
        <form>
          <div class="input-group">
            <div class="input-group-append">
              <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
              </button>
            </div>
            <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">
            
          </div>
        </form>
      </div>
      
      <li class="dropdown-notifications" id="notifWindow">
        @if(auth()->user()->unreadNotifications->isEmpty())
          <div class="dropdown-toggleNotif" onclick="toggleDropdown(this)">
            <i class="fa fa-bell" id="unreadCount2" data-count="" tabindex="0" role="button"></i>
          </div>
        @else
          <div class="dropdown-toggleNotif" onclick="toggleDropdown(this)">
            <i class="fa fa-bell notification-icon"  id="unreadCount" data-count={{auth()->user()->unreadNotifications->count()}} tabindex="0" role="button"></i>
          </div>
        @endif   
        
        <div class="dropdown-container">

          <div class="dropdown-toolbar">
            <div class="dropdown-toolbar-actions" id="markAll">
              <a href="javascript:void(0)">Mark all as read</a>
            </div>
            <h3 class="dropdown-toolbar-title" id="notificationTitleCount">Notifications ({{auth()->user()->unreadNotifications->count()}})</h3>
          </div><!-- /dropdown-toolbar -->

          <ul class="dropdown-menu">
            @foreach (auth()->user()->unreadNotifications as $index => $notification)
              <li class="notification">
                <a href="javascript:void(0)">
                  <span class="read-notification" data-notifID="{{$notification->id}}" data-countid="{{$index}}" onclick="readNotif(this)">&times;</span>
                </a>
                <div class="media">
                  <img src="{{ asset('temp/vendor/css/admin/img/avatar.svg') }}" class="mr-2 pt-2 img-circle" alt="Name" style="width: 50px; height: 50px;">
                  <div class="media-body"> 
                    <strong class="notification-title">{{$notification->data['Title']}}</strong>
                    <p class="notification-desc">{{$notification->data['Body']}}</p>
          
                    <div class="notification-meta">
                      <small class="timestamp">{{$notification->created_at}}</small>
                    </div>
                  </div>
                </div>
              </li>
            @endforeach
          </ul>

          <div class="dropdown-footer text-center">
            <a href="#">View All</a>
          </div><!-- /dropdown-footer -->

        </div><!-- /dropdown-container -->
      </li><!-- /dropdown -->

      <!-- Navbar -->
      <div class="navbarUserdropdown" onclick="userDropdwn(this)" id="userDropdown">
        <ul class="navbar-nav ml-auto ml-md-0">
          <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#">
              <i class="fas fa-user-circle fa-fw"></i>
              @auth
                <span>
                  {{ Auth::user()->username }} {{ Auth::user()->roles->isNotEmpty() ? Auth::user()->roles->first()->username : "" }}
                </span>
              @endauth
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
            </div>
          </li>
        </ul>
      </div>
      
    </nav>
    <!-- CONTENT NEXT TO SIDEBAR -->
    <div class="nextTosidebar">
      <!-- SIDEBAR -->
      <div class="sidebar" id="sidebarID">
        <ul class="navbar-nav">
          <!--<li class="nav-item top">
            <a class="nav-link" href="/">
              <i class="fas fa-home"></i>
              <span>Logo</span>
            </a>
          </li>-->
          <li class="nav-item" data-navitemid="1" onclick="openSubmenu(this)">
            <a class="nav-link" href="javascript:void(0)">
              <i class="fas fa-fw fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
            <div class="testMenu">
              <div class="sub-menu-header">Dashboard</div>
              <div class="sub-menu-icon"><i class="fas fa-fw fa-tachometer-alt"></i></div>
              <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li><a href="/admin/dashboard">Dashboard</a></li>
                    <li><a href="/admin/route">All Routes</a></li>
                    <li><a href="/admin/schedule">All Schedules</a></li>
                    <li><a href="/admin/booking">All Bookings</a></li>
                    <li><a href="/admin/ticket-type">All Ticket Types</a></li>
                </ul>
              </div>
            </div>
          </li>
          <li class="nav-item" data-navitemid="2" onclick="openSubmenu(this)">
            <a class="nav-link" href="javascript:void(0)">
              <i class="far fa-id-badge"></i>
              <span>Profile</span>
            </a>
            <div class="testMenu">
              <div class="sub-menu-header">Profile</div>
              <div class="sub-menu-icon"><i class="far fa-id-badge"></i></div>
              <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li><a href="/admin/user/{{auth()->user()->id}}/profile/">Profile</a></li>
                    <li><a href="/admin/user/{{auth()->user()->id}}/profile/my-vessel">My Vessels</a></li>
                    <li><a href="/admin/user/{{auth()->user()->id}}/profile/my-booking">My Bookings</a></li>
                    <li><a href="/admin/user/{{auth()->user()->id}}/profile/my-schedule">My Schedules</a></li>
                    <li><a href="/admin/user/{{auth()->user()->id}}/profile/my-route">My Routes</a></li>
                    <li><a href="/admin/user/{{auth()->user()->id}}/profile/my-ticket-type">My Ticket Types</a></li>
                    @canany(['isAdmin','isStaff','isAgent'])
                      <li><a href="/admin/user/{{auth()->user()->id}}/profile/vessel-assigned-to">Vessels Assigned To</a></li>
                      <li><a href="/admin/user/{{auth()->user()->id}}/profile/my-agent-island">Islands Assigned To</a></li>
                    @endcanany
                    @canany(['isAdmin','isStaff','isMerchant'])
                      <li><a href="/admin/user/{{auth()->user()->id}}/profile/my-assigned-vessel">My Assigned Vessels</a></li>
                    @endcanany
                </ul>
              </div>
            </div>
          </li>
          <li class="nav-item" data-navitemid="3" onclick="openSubmenu(this)">
            <a class="nav-link" href="javascript:void(0)">
              <i class="fas fa-users"></i>
              <span>User</span>
            </a>
            <div class="testMenu">
              <div class="sub-menu-header">User</div>
              <div class="sub-menu-icon"><i class="fas fa-users"></i></div>
              <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li><a href="/admin/user/{{ auth()->user()->id }}/profile">My Profile</a></li>
                    @canany(['isAdmin','isStaff'])
                      <li><a href="/admin/user">All Users</a></li>
                      <li><a href="/admin/role">All Roles</a></li>
                    @endcanany
                </ul>
              </div>
            </div>
          </li>
          <li class="nav-item" data-navitemid="4" onclick="openSubmenu(this)">
            <a class="nav-link" href="javascript:void(0)">
              <i class="fas fa-ship"></i>
              <span>Vessel</span>
            </a>
            <div class="testMenu">
              <div class="sub-menu-header">Vessels</div>
              <div class="sub-menu-icon"><i class="fas fa-ship"></i></div>
              <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li><a href="/admin/vessel">All Vessels</a></li>
                    <li><a href="/admin/vessel/assign-vessel">Assign Vessel</a></li>
                </ul>
              </div>
            </div>
          </li>
          <li class="nav-item" data-navitemid="5" onclick="openSubmenu(this)">
            <a class="nav-link" href="javascript:void(0)">
              <i class="fas fa-map-marker-alt"></i>
              <span>Island</span>
            </a>
            <div class="testMenu">
              <div class="sub-menu-header">Islands</div>
              <div class="sub-menu-icon"><i class="fas fa-map-marker-alt"></i></div>
              <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li><a href="/admin/island">All Islands</a></li>
                    <li><a href="/admin/island/agent-island">Assign Agent to Island</a></li>
                </ul>
              </div>
            </div>
          </li>
          
        </ul>
      </div>
      
      <div class="gridcontainer">
        

        <!-- MAIN CONTENT -->
        <div class="mainContent">
          @yield('content')
        </div>
        
        <!-- FOOTER -->
        <footer class="sticky-footer">
          <div class="text-center my-auto">
            <span>Copyright © Your Website @php echo date('Y') @endphp</span>
          </div>
        </footer>
      </div>
    </div>
    
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

            <a class="btn btn-primary" href="#"
                  onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();" data-dismiss="modal">
                {{ __('Logout') }}
            </a>
            <form id="logout-form" type="submit" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>

            {{-- <a class="btn btn-primary" href="login.html">Logout</a> --}}
          </div>
        </div>
      </div>
    </div>
    
    <!-- Jquery 3.6.0 JS -->
    <script src="{{asset ('temp/vendor/jquery/jquery.min.js')}}"></script>
    
    <!-- Jquery 1.4.1 JS -->
    <script src="{{asset ('temp/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Bootstrap 4.5.2 JS -->
    <script src="{{asset ('temp/vendor/bootstrap/js/bootstrap.min.js')}}"></script>

    
    <!-- Moment 2.21.0 JS -->
    <script src="{{asset ('temp/vendor/moment/moment.min.js')}}"></script>

    <!-- APP JS for Echo -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Custom bootstrap datepicker JS for bootstrap version 4 -->
    <script src="{{asset ('temp/vendor/datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
    
    <!-- Type ahead bootstrap 3 typeahead@4.0.2 
    <script src="{{asset ('temp/vendor/typeahead/bootstrap3-typeahead.min.js')}}"></script>-->

    
    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <!-- Datatables 1.10.24 JS -->
    <script src="{{asset ('temp/vendor/datatables/js/jquery.dataTables.min.js')}}"></script>

    
    <!-- Datatables fixedHeader 3.1.8 JS -->
    
    
    <!-- Datatables responsive 2.2.7 JS -->
    <script src="{{asset ('temp/vendor/datatables/responsive/js/dataTables.responsive.min.js')}}"></script>
    
    <!-- Datatables responsive bootstrap 2.2.7 JS -->
    <script src="{{asset ('temp/vendor/datatables/responsive/js/responsive.bootstrap.min.js')}}"></script>

    <!-- Datatables 1.10.24 bootstrap 4 JS -->
    <script src="{{asset ('temp/vendor/datatables/js/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Select2 JS-->
    <script src="{{asset ('temp/vendor/select2/js/select2.min.js')}}"></script>

    <!-- Sweetalert2 JS 10.16.0 -->
    <script src="{{asset ('temp/vendor/sweetalert2/js/sweetalert2.min.js')}}"></script>

    <!-- Chart JS 2.7.0 -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.js'></script>

    <!-- Tagify JS 4.1.1 -->
    <script src="{{asset ('temp/vendor/tagify/js/tagify.min.js')}}"></script>

    <!-- Full calendar JS 5.8.0 -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.8.0/main.min.js"></script>

    
    <script type="text/javascript">
      $(document).ready(function () {//datatable
          $('#page-top').show();
          $('#msg').hide();

          $("#sidebarToggle").on('click', function (e) {
            e.preventDefault();
            
            $(".sidebar").toggleClass("toggled");
          });

          $('.myTable').DataTable({
            columnDefs: [
              { orderable: false, targets: -1 },
              { width: 120, targets: -1 }
            ],
            responsive: true,
            autoWidth: true,
            language: {searchPlaceholder: "Search",search: ""},
            dom: "<'row'<'col-sm-4'f><'col-sm-8'l>>tr" +
         "<'row'<'col-sm-4'i><'col-sm-8'p>>"
          });
            
      });
    </script>
    
    <script defer>
        //adding active class in sidebar dropdown
        var flag;
        //var closeThis = false;
        var navitem;
        var navitemID;
        $('.sidebar .nav-item').hover(function() {//when hover over nav item add class
          $('ul >li').removeClass('active')
          $(this).addClass('active');
          flag = 0;
          navitemID = $(this).data('navitemid');
          navitem = document.getElementsByClassName("nav-item")[navitemID];
        });
        
        /*
        $('.nav-item').click(function(){//when nav item is clicked toggle the boolean
          closeThis = !closeThis;
          if(closeThis){
            $('ul >li').removeClass('active');
          }else{
            $(this).addClass('active');
          }
          
        });
        */
        function openSubmenu(e){//click version when hover isn't possible
          if(e.target !== e){
            $('ul >li').removeClass('active')
            $(e).addClass('active');
            navitemID = $(e).data('navitemid');
            navitem = document.getElementsByClassName("nav-item")[navitemID];
            flag = 0;
            console.log("working");
          }
        }
        //when clicked inside element stay open but when click outside close
        document.addEventListener("click", function(event){
          if(flag == 0){
              var isClickInsideElement = navitem.contains(event.target);
              if (!isClickInsideElement) {//if it's not then remove the dropdown class so it won't show 
                flag = 1;
                //submenu.style.display = "none";
                $('ul >li').removeClass('active');
                //submenu.style.display = "block";
                event.stopPropagation();
              }
          }
          
        });
    </script>

    <script defer>
      function userDropdwn(e){
        $('.navbarUserdropdown .dropdown-menu.dropdown-menu-right').addClass('show');
        var userdropdwn = document.getElementById('userDropdown');
        var flag = 0;
        document.addEventListener("click", function(event){
          if(flag == 0){
              var isClickInsideElement = userdropdwn.contains(event.target);
              if (!isClickInsideElement) {//if it's not then remove the dropdown class so it won't show 
                flag = 1;
                //submenu.style.display = "none";
                $('.navbarUserdropdown .dropdown-menu.dropdown-menu-right').removeClass('show');
                //submenu.style.display = "block";
                event.stopPropagation();
              }
          }
          
        });
      }
    </script>

    <script>
      // Your web app's Firebase configuration
      var firebaseConfig = {
          apiKey: "AIzaSyBL5xZRLluTaCg94Aw9hfTI_bQ6HQSuUS8",
          authDomain: "ticket-system-notification.firebaseapp.com",
          projectId: "ticket-system-notification",
          storageBucket: "ticket-system-notification.appspot.com",
          messagingSenderId: "573089053325",
          appId: "1:573089053325:web:221a8f9dd5343a1b07c0c4",
          measurementId: "G-YRSNXB6KKY"

      };
      // Initialize Firebase
      firebase.initializeApp(firebaseConfig);

      const messaging = firebase.messaging();

      function initFirebaseMessagingRegistration() {
          messaging.requestPermission().then(function () {
              return messaging.getToken()
          }).then(function(token) {
              
              axios.post("{{ route('fcmToken') }}",{
                  _method:"PATCH",
                  token
              }).then(({data})=>{
                  console.log(data)//show data
              }).catch(({response:{data}})=>{
                  console.error(data)//show error
              })

          }).catch(function (err) {
              console.log(`Token Error :: ${err}`);
          });
      }

      initFirebaseMessagingRegistration();

      messaging.onMessage(function({data:{body,title}}){
          new Notification(title, {body});
      });
    </script>
        
    <script>
        //toggle drop down function. There was 2 dropdowns(logout and notif) that's why I put this one inside function
        function toggleDropdown(d){
          let notifWindowD = document.getElementById("notifWindow");//get the body
          $(d).parent().toggleClass('show');//toggle so it would show
          
          let flag = 0;//flag to stop the things happening inside event
          
          document.onclick = function(event){
            if(flag == 0){
              var isClickInsideElement = notifWindowD.contains(event.target);//if target contains body's element then it is inside the notif window
              if (!isClickInsideElement) {//if it's not then remove the dropdown class so it won't show 
                flag = 1;
                $(d).parent().removeClass('show');
              }
            }
            
          };
          
        }
    </script>

    <script>
      var notifWindow            = document.getElementById("notifWindow");
      var notificationsWrapper   = $('.dropdown-notifications');//get notification wrapper
      var notificationsCountElem = notificationsWrapper.find('i[data-count]');//find data elem
      var notificationsCount     = parseInt(notificationsCountElem.data('count'));//get it's number
      var notifCountIcon         = document.getElementById("unreadCount");//id to hide the notif icon when there are no notifications
      var notifCountIcon2        = document.getElementById("unreadCount2");//id to show the notif icon when there are notifications
      var markAllRead            = document.getElementById("markAll");//id to hide the mark all read
      var notifications          = notificationsWrapper.find('ul.dropdown-menu');//the notification body where we are going to 

      var indexCount = notificationsCount - 1;//if there are no notifs then it will give NaN. But if there is notif then subtract it by 1 so it matches the class index
      var userId = {!! json_encode(auth()->id(), JSON_HEX_TAG) !!};//getting user id

      if(notificationsCount<=0 || isNaN(notificationsCount)){//if it notif is 0 or is NaN (when empty it gives this)
        $(markAllRead).hide();//hide the mark all read
        notificationsCount = 0;
        indexCount = -1;//set indexCount to -1 if it is NaN so when a new notif comes it will add by 1 so index will be 0
      }

      //when mark all read is clicked
      $('.dropdown-toolbar-actions').on('click', function (event) {
          $.ajax({
              type:'get',
              url:"/notification/markAllRead",//route
              success: function(data) {
                  
              },
              error: function (err) {//if there is error 
                  alert("error"); 
              }
          });
          var notif = document.getElementsByClassName('notification');//get all notification bodies in window
          $(notif).hide();//hide it
          $(markAllRead).hide();//hide the mark all read
          notificationsCountElem.attr('data-count', 0);//changing notif icon count
          if(notifCountIcon != null){
            notifCountIcon.classList.remove("notification-icon");//remove notification icon number so it won't show 0 and red circle
          }else{
            notifCountIcon2.classList.remove("notification-icon");//remove notification icon number2 so it won't show 0 and red circle
          }
          
          document.getElementById("notificationTitleCount").innerHTML = "Notifcations (0)";//to make sure notif title counter stays 0
      });

      //the moment notification event happens this will run. Using echo for 2 way communication between server and client
      Echo.private('App.Models.User.' + userId)
      .notification((notification) => {
          indexCount += 1;//adding by 1 so we can identify each class
          //what we are going to append when we recieve a notification
          let newNotificationHtml = `
          <li class="notification">
            <a href="javascript:void(0)">
              <span class="read-notification" data-notifID="`+ notification.id +`" data-countid="`+ indexCount +`" onclick="readNotif(this)">&times;</span>
            </a>
            <div class="media">
              <img src="{{ asset('temp/vendor/css/admin/img/avatar.svg') }}" class="mr-2 pt-2 img-circle" alt="Name" style="width: 50px; height: 50px;">
              <div class="media-body"> 
                <strong class="notification-title">`+ notification.Title +`</strong>
                <p class="notification-desc">`+ notification.Body +`</p>
      
                <div class="notification-meta">
                  <small class="timestamp">`+ notification.Created_date +`</small>
                </div>
              </div>
            </div>
          </li>
        `;
          //appending  
          notifications.append(newNotificationHtml);
          notificationsCount += 1;//increase notification count
          if(notifCountIcon2 != null){
            notifCountIcon2.classList.remove("notification-icon");//remove notification icon number so it won't show 0 and red circle
            notifCountIcon2.classList.add("notification-icon");
          }
          
          notificationsCountElem.attr('data-count', notificationsCount);//giving the new value to the icon
          notificationsWrapper.find('.notif-count').text(notificationsCount);//giving the new value to the icon's text
          
          $(markAllRead).show();//show mark all read when there is a new notification
      });

      function readNotif(e){//read a notification 
        let countID = e.getAttribute('data-countid');//notif count
        let notifID = e.getAttribute('data-notifID');//notif id


        $.ajax({
            type:'get',
            url:"/notification/read",//route
            data:{//data we are sending
              notifID:notifID
            },
            success: function(data) {
                
            },
            error: function (err) {//if there is error 
                alert("error"); 
            }
        });

        //find count id and get the closest notification class of it so we can hide it
        $('.dropdown-menu').find(`*[data-countid="`+ countID +`"]`).closest('.notification').hide('slow', function() {//hiding it
          notificationsCount -= 1;//decrementing notif count
          document.getElementById("notificationTitleCount").innerHTML = "Notifcations ("+notificationsCount+")";//changing notif title count
          notificationsCountElem.attr('data-count', notificationsCount);//changing notif icon count
          if(notificationsCount <= 0){//if it is 0 or below then
            if(notifCountIcon != null){
              notifCountIcon.classList.remove("notification-icon");//remove notification icon number so it won't show 0 and red circle
            }else{
              notifCountIcon2.classList.remove("notification-icon");//remove notification icon number so it won't show 0 and red circle
            }
            document.getElementById("notificationTitleCount").innerHTML = "Notifcations (0)";//to make sure notif title counter stays 0
            $(markAllRead).hide();//hide mark all read if notification count is below 0
          }
        });
        
        
      }

    </script>

    <!--PASSWORD RESET ALERT -->
    <script>
      @if(!empty(Session::get('password-reset-status')))
          var popupId = "{{ uniqid() }}";
          if(!sessionStorage.getItem('shown-' + popupId)) {
              const Toast = Swal.mixin({
              toast: true,
              position: 'bottom-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            })

            Toast.fire({
              icon: 'success',
              title: 'Your password has been reset!'
            })
          }
          sessionStorage.setItem('shown-' + popupId, '1');
      @endif
    </script>

    <!--EMAIL VERIFIED ALERT -->
    <script>
      @if(!empty(Session::get('verified')))
          var popupId = "{{ uniqid() }}";
          if(!sessionStorage.getItem('shown-' + popupId)) {
              const Toast = Swal.mixin({
              toast: true,
              position: 'bottom-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            })

            Toast.fire({
              icon: 'success',
              title: 'Your email has been verified!'
            })
          }
          sessionStorage.setItem('shown-' + popupId, '1');
      @endif
    </script>

    <!--UNAUTHORIZED ALERT -->
    <script>
      @if(!empty(Session::get('no-auth')))
          var popupId = "{{ uniqid() }}";
          if(!sessionStorage.getItem('shown-' + popupId)) {
              Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'You cannot access that page',
                  showConfirmButton: false,
                  timer: 1200
              })
          }
          sessionStorage.setItem('shown-' + popupId, '1');
      @endif
    </script>

    <!--CREATE ALERT -->
    @yield('js_booking_create_alert')
    @yield('js_island_create_alert')
    @yield('js_role_create_alert')
    @yield('js_route_create_alert')
    @yield('js_schedule_create_alert')
    @yield('js_ticket_type_create_alert')
    @yield('js_user_create_alert')
    @yield('js_vessel_create_alert')
    @yield('js_vessel_type_create_alert')
    @yield('js_my_vessel_create_alert')
    @yield('js_my_booking_create_alert')
    @yield('js_my_route_create_alert')
    @yield('js_my_schedule_create_alert')
    @yield('js_assign_vessel_create_alert')
    @yield('js_my_assign_vessel_create_alert')
    @yield('js_agent_island_create_alert')
    @yield('js_my_agent_island_create_alert')

    <!--EDIT ALERT -->
    @yield('js_booking_edit_alert')
    @yield('js_island_edit_alert')
    @yield('js_role_edit_alert')
    @yield('js_route_edit_alert')
    @yield('js_schedule_edit_alert')
    @yield('js_ticket_type_edit_alert')
    @yield('js_user_edit_alert')
    @yield('js_vessel_edit_alert')
    @yield('js_vessel_type_edit_alert')
    @yield('js_profile_edit_alert')
    @yield('js_my_vessel_edit_alert')
    @yield('js_my_booking_edit_alert')
    @yield('js_my_route_edit_alert')
    @yield('js_my_schedule_edit_alert')
    @yield('js_assign_vessel_edit_alert')
    @yield('js_my_assign_vessel_edit_alert')
    @yield('js_agent_island_edit_alert')
    @yield('js_my_agent_island_edit_alert')

    <!-- DELETE ALERT -->
    @yield('js_delete_booking')
    @yield('js_delete_island')
    @yield('js_delete_role')
    @yield('js_delete_route')
    @yield('js_delete_schedule')
    @yield('js_delete_ticket_type')
    @yield('js_delete_user')
    @yield('js_delete_vessel')
    @yield('js_delete_vessel_type')
    @yield('js_my_delete_my_vessel')
    @yield('js_delete_my_booking')
    @yield('js_delete_my_route')
    @yield('js_delete_my_schedule')
    @yield('js_delete_assign_vessel')
    @yield('js_my_delete_my_assign_vessel')
    @yield('js_delete_agent_island')
    @yield('js_my_delete_my_agent_island')

    <!-- USER -->
    @yield('js_user_create')
    @yield('js_user_edit')

    <!-- VESSEL -->
    @yield('js_vessel_create_select')
    @yield('js_vessel_edit_select')

    <!-- ROLE -->
    @yield('js_role_create_tags')
    @yield('js_role_create_permissionShow')
    @yield('js_role_edit_tags')
    @yield('js_role_edit_permissionShow')
    
    <!-- ROUTE -->
    @yield('js_route_schedule_pop')
    @yield('js_route_create_select')
    @yield('js_route_edit_select')
    @yield('js_route_create_tagify')
    @yield('js_route_edit_tagify')
    
    <!-- BOOKING -->
    @yield('js_booking_create_select')
    @yield('js_booking_edit_select')

    <!-- SCHEDULE -->
    @yield('js_schedule_create_datetimepicker')
    @yield('js_schedule_edit_datetimepicker')
    @yield('js_schedule_booking_pop')

    <!-- MY BOOKING -->
    @yield('js_my_booking_create_select')
    @yield('js_my_booking_edit_select')

    <!-- MY ROUTE -->
    @yield('js_my_route_schedule_pop')
    @yield('js_my_route_create_tags')
    @yield('js_my_route_edit_select')

    <!-- MY SCHEDULE -->
    @yield('js_my_schedule_create_datetimepicker')
    @yield('js_my_schedule_edit_datetimepicker')
    @yield('js_my_schedule_booking_pop')

    <!-- ASSIGN VESSEL -->
    @yield('js_assign_vessel_create_select')
    @yield('js_assign_vessel_edit_select')

    <!-- MY ASSIGN VESSEL -->
    @yield('js_my_assign_vessel_create_select')
    @yield('js_my_assign_vessel_edit_select')

    <!-- ISLAND -->
    @yield('js_island_create_select')
    @yield('js_island_edit_select')

    <!-- AGENT ISLAND -->
    @yield('js_agent_island_create_select')
    @yield('js_agent_island_edit_select')

    <!-- MY AGENT ISLAND -->
    @yield('js_my_agent_island_create_select')
    @yield('js_my_agent_island_edit_select')

    @yield('js_ajax')

    @yield('js_tooltip')

    @yield('js_dashboard_chart')

  </body>
  
  
    
</html>
    