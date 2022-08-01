@extends('admin.layouts.dashboard')

@section('content')
    

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li> 
                @endforeach
            </ul>
        </div>
    @endif

    <div class="roleCreateGridContainer">
        <div class="gridRoleCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/role" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <h5 class="form-header">Create Role</h5>
                        <div class="form-desc">Create new roles with permissions</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> Role Name</label>
                            <div class="col-sm-8"><input class="form-control" id="role_name" name="role_name" type="text" placeholder="Role Name" value="{{ old('role_name') }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> Role Slug</label>
                            <div class="col-sm-8"><input class="form-control" id="role_slug" name="role_slug" type="text" placeholder="Role Slug" value="{{ old('role_slug') }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Assign Permission</label>
                            <div class="col-sm-8"><input data-nametag="tags-manual-suggestions" name="permission" class="form-control" id="permission_roles" value="{{ old('permission_roles') }}"></div>
                        </div>
                        
                
                      <fieldset class="form-group">
                        <legend><span>Select Permission</span></legend>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Permissions</label>
                            <div class="col-sm-8 toTheRight">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" data-checkbox-id="admin" id="admin" onclick="checkboxAll(this)">
                                    <label class="custom-control-label" for="admin">Admin</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" data-checkbox-id="staff" id="staff" onclick="checkboxAll(this)">
                                    <label class="custom-control-label" for="staff">Staff</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" data-checkbox-id="merchant" id="merchant" onclick="checkboxAll(this)">
                                    <label class="custom-control-label" for="merchant">Merchant</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" data-checkbox-id="agent" id="agent" onclick="checkboxAll(this)">
                                    <label class="custom-control-label" for="agent">Agent</label>
                                </div>
                                <div class="btn btn-outline-dark" data-checkbox-id="uncheckall" id="uncheckall" onclick="uncheckAll(this)">
                                    Uncheck all
                                </div>
                                
                            </div>
                        </div>
                        
                      </fieldset>
                      <div class="form-group row">
                            <div id="permissions_box">
                                @foreach ($permissions as $permission)
                                    <div class="custom-control custom-checkbox pr-3">                     
                                        <input class="custom-control-input" type="checkbox" name="permissions[]" data-role-id="{{$permission->id}}" data-role-slug="{{$permission->slug}}" data-role-name="{{$permission->name}}" id="{{$permission->slug}}" value="{{$permission->id}}" onchange="permissionShow(this)">
                                        <label class="custom-control-label" for="{{$permission->slug}}">{{$permission->name}}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/role">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>


    @section('css_role_create_tags')
        <link rel="stylesheet" href="{{ asset('temp/vendor/css/admin/bootstrap-tagsinput.css') }}">
    @endsection


    @section('js_role_create_permissionShow')
        <script>
            var permissionNames = @json($permissionNames);

            var input = document.querySelector('input[data-nametag=tags-manual-suggestions]'),
                // init Tagify script on the above inputs
                
                tagify = new Tagify(input, {
                    whitelist : permissionNames.toString().split(","),
                    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', '),
                    dropdown: {
                        position: "all",
                        maxItems: Infinity,
                        enabled: 0,
                        closeOnSelect: false, 
                        classname: "customSuggestionsList"
                    },
                    enforceWhitelist: true
                })
                tagify.on("dropdown:show", onSuggestionsListUpdate)
                    .on("dropdown:hide", onSuggestionsListHide)
                    .on('dropdown:scroll', onDropdownScroll)
                    .on('add', onAddTag)
                    .on('remove', onRemoveTag)

                // ES2015 argument destructuring
                function onSuggestionsListUpdate({ detail:suggestionsElm }){
                    //console.log(  suggestionsElm  )
                }

                function onSuggestionsListHide(){
                    //console.log("hide dropdown")
                }

                function onDropdownScroll(e){
                    //console.log(e.detail)
                }

                //on add trigger this event. It will check the box
                function onAddTag(e){
                    var changeString = e.detail.data.value;
                    changeString = changeString.replace(/\W+(?!$)/g, '-').toLowerCase();//replaces space with dash
                    $('#' + changeString).prop("checked", true);
                    //tagify.off('add', onAddTag) // exmaple of removing a custom Tagify event
                }

                //on remove trigger this event. It will uncheck the box
                function onRemoveTag(e){
                    var changeString = e.detail.data.value;
                    changeString = changeString.replace(/\W+(?!$)/g, '-').toLowerCase();//replaces space with dash
                    $('#' + changeString).prop("checked", false);
                    //tagify.off('remove', onRemoveTag) // exmaple of removing a custom Tagify event
                }
        </script>
        <script defer>
            //This function is to check all permission check boxes of admin,staff,merchant,agent
            function uncheckAll(){
                //Separating them so there will be animation effect when removing
                tagify.removeTags("Create Booking");
                tagify.removeTags("Edit Booking");
                tagify.removeTags("Delete Booking");
                tagify.removeTags("Create Island");
                tagify.removeTags("Edit Island");
                tagify.removeTags("Delete Island");
                tagify.removeTags("Create Role");
                tagify.removeTags("Edit Role");
                tagify.removeTags("Delete Role");
                tagify.removeTags("Create Route");
                tagify.removeTags("Edit Route");
                tagify.removeTags("Delete Route");
                tagify.removeTags("Create Schedule");
                tagify.removeTags("Edit Schedule");
                tagify.removeTags("Delete Schedule");
                tagify.removeTags("Create Ticket Type");
                tagify.removeTags("Edit Ticket Type");
                tagify.removeTags("Delete Ticket Type");
                tagify.removeTags("Create User");
                tagify.removeTags("Edit User");
                tagify.removeTags("Delete User");
                tagify.removeTags("Create Vessel");
                tagify.removeTags("Edit Vessel");
                tagify.removeTags("Delete Vessel");
                tagify.removeTags("Create Vessel Type");
                tagify.removeTags("Edit Vessel Type");
                tagify.removeTags("Delete Vessel Type");
                tagify.removeTags("Create Assign Vessel");
                tagify.removeTags("Edit Assign Vessel");
                tagify.removeTags("Delete Assign Vessel");
                tagify.removeTags("Create Agent Island");
                tagify.removeTags("Edit Agent Island");
                tagify.removeTags("Delete Agent Island");
                tagify.removeTags("Edit Profile");

                $("#admin").prop("checked", false);
                $("#staff").prop("checked", false);
                $("#merchant").prop("checked", false);
                $("#agent").prop("checked", false);
            }

            function checkboxAll(element){
                var checkbox = element.getAttribute('data-checkbox-id');
                switch(checkbox){
                    case "admin":
                        if (element.checked) {
                            tagify.addTags([
                                "Create Booking","Edit Booking","Delete Booking",
                                "Create Island","Edit Island","Delete Island",
                                "Create Role","Edit Role","Delete Role",
                                "Create Route","Edit Route","Delete Route",
                                "Create Schedule","Edit Schedule","Delete Schedule",
                                "Create Ticket Type","Edit Ticket Type","Delete Ticket Type",
                                "Create User","Edit User","Delete User",
                                "Create Vessel","Edit Vessel","Delete Vessel",
                                "Create Vessel Type","Edit Vessel Type","Delete Vessel Type",
                                "Create Assign Vessel","Edit Assign Vessel","Delete Assign Vessel",
                                "Create Agent Island","Edit Agent Island","Delete Agent Island",
                                "Edit Profile"
                            ]);
                            $("#uncheckall").prop("checked", false);
                        } else {
                            //Separating them so there will be animation effect when removing
                            tagify.removeTags("Create Booking");
                            tagify.removeTags("Edit Booking");
                            tagify.removeTags("Delete Booking");
                            tagify.removeTags("Create Island");
                            tagify.removeTags("Edit Island");
                            tagify.removeTags("Delete Island");
                            tagify.removeTags("Create Role");
                            tagify.removeTags("Edit Role");
                            tagify.removeTags("Delete Role");
                            tagify.removeTags("Create Route");
                            tagify.removeTags("Edit Route");
                            tagify.removeTags("Delete Route");
                            tagify.removeTags("Create Schedule");
                            tagify.removeTags("Edit Schedule");
                            tagify.removeTags("Delete Schedule");
                            tagify.removeTags("Create Ticket Type");
                            tagify.removeTags("Edit Ticket Type");
                            tagify.removeTags("Delete Ticket Type");
                            tagify.removeTags("Create User");
                            tagify.removeTags("Edit User");
                            tagify.removeTags("Delete User");
                            tagify.removeTags("Create Vessel");
                            tagify.removeTags("Edit Vessel");
                            tagify.removeTags("Delete Vessel");
                            tagify.removeTags("Create Vessel Type");
                            tagify.removeTags("Edit Vessel Type");
                            tagify.removeTags("Delete Vessel Type");
                            tagify.removeTags("Create Assign Vessel");
                            tagify.removeTags("Edit Assign Vessel");
                            tagify.removeTags("Delete Assign Vessel");
                            tagify.removeTags("Create Agent Island");
                            tagify.removeTags("Edit Agent Island");
                            tagify.removeTags("Delete Agent Island");
                            tagify.removeTags("Edit Profile");
                        }
                        break;
                    case "staff":
                        if (element.checked) {
                            tagify.addTags([
                                "Create Booking","Edit Booking","Delete Booking",
                                "Create Route","Edit Route","Delete Route",
                                "Create Schedule","Edit Schedule","Delete Schedule",
                                "Create Vessel","Edit Vessel","Delete Vessel"
                            ]);
                            $("#uncheckall").prop("checked", false);
                        } else {
                            tagify.removeTags("Create Booking");
                            tagify.removeTags("Edit Booking");
                            tagify.removeTags("Delete Booking");
                            tagify.removeTags("Create Route");
                            tagify.removeTags("Edit Route");
                            tagify.removeTags("Delete Route");
                            tagify.removeTags("Create Schedule");
                            tagify.removeTags("Edit Schedule");
                            tagify.removeTags("Delete Schedule");
                            tagify.removeTags("Create Vessel");
                            tagify.removeTags("Edit Vessel");
                            tagify.removeTags("Delete Vessel");
                        }
                        break;
                    case "merchant":
                        if (element.checked) {
                            tagify.addTags([
                                "Create Booking","Edit Booking","Delete Booking",
                                "Create Vessel","Edit Vessel","Delete Vessel"
                            ]);
                            $("#uncheckall").prop("checked", false);
                            
                        } else {
                            //Separating them so there will be animation effect when removing
                            tagify.removeTags("Create Booking");
                            tagify.removeTags("Edit Booking");
                            tagify.removeTags("Delete Booking");
                            tagify.removeTags("Create Vessel");
                            tagify.removeTags("Edit Vessel");
                            tagify.removeTags("Delete Vessel");
                        }
                        break;
                    case "agent":
                        if (element.checked) {
                            //tagify.addTags([
                            ///    "Create Booking","Edit Booking","Delete Booking",
                            //    "Create Schedule","Edit Schedule","Delete Schedule"
                            //]);
                            tagify.addTags("Create Booking");
                            tagify.addTags("Edit Booking");
                            tagify.addTags("Delete Booking");
                            tagify.addTags("Create Schedule");
                            tagify.addTags("Edit Schedule");
                            tagify.addTags("Delete Schedule");
                            $("#uncheckall").prop("checked", false);
                        } else {
                            //Separating them so there will be animation effect when removing
                            tagify.removeTags("Create Booking");
                            tagify.removeTags("Edit Booking");
                            tagify.removeTags("Delete Booking");
                            tagify.removeTags("Create Schedule");
                            tagify.removeTags("Edit Schedule");
                            tagify.removeTags("Delete Schedule");
                        }
                        break;
                    default:
                        break;
                }
            }
            //This function adds the permission  when user individually clicks on the permission box
            function permissionShow(element){
                var name = element.getAttribute('data-role-name');
                if (element.checked) {
                    tagify.addTags(name);
                } else {
                    //$('#permission_roles').tagsinput('remove', name);
                    tagify.removeTags(name);
                }
            }
            
        </script>
    @endsection

    @section('js_role_create_tags')
        <script src="{{ asset('temp/vendor/js/admin/bootstrap-tagsinput.js') }}"></script>

        <script defer>
            //This function is for automatically filling the slug field when name is entered
            $(document).ready(function(){
                $('#role_name').keyup(function(e){
                    var str = $('#role_name').val();
                    str = str.replace(/\W+(?!$)/g, '-').toLowerCase();//replaces space with dash
                    $('#role_slug').val(str);
                    $('#role_slug').attr('placeholder', str);
                });
            });
            
        </script>

    @endsection

@endsection
