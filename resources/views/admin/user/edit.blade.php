@extends('admin.layouts.dashboard')

@section('content')

    

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="userCreateGridContainer">
        <div class="gridUserCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/user/{{ $user->id }}" enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf()
                        <h5 class="form-header">Edit Users</h5>
                        <div class="form-desc">Edit user's information, role & permissions</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> First Name</label>
                            <div class="col-sm-8"><input class="form-control" name="first_name" type="text" placeholder="First Name" value="{{ $user->first_name }}"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> Last Name</label>
                            <div class="col-sm-8"><input class="form-control" name="last_name" type="text" placeholder="Last Name" value="{{ $user->last_name }}"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Username</label>
                            <div class="col-sm-8"><input type="text" name="username" class="form-control" id="username" placeholder="Username" value="{{ $user->username }}"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Email</label>
                            <div class="col-sm-8"><input type="email" name="email" class="form-control" id="email" placeholder="Email" value="{{ $user->email }}"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Password</label>
                            <div class="col-sm-8"><input type="password" name="password" class="form-control" id="password" placeholder="Password" minlength="4"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Password Again</label>
                            <div class="col-sm-8"><input type="password" name="password_confirmation" class="form-control" placeholder="Password" id="password_confirmation" ></div>
                        </div>
                
                      <fieldset class="form-group">
                        <legend><span>Role & Permission</span></legend>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Role</label>
                            <div class="col-sm-8">
                                <select class="role custom-select" name="role" id="role">
                                    <option value="">Select Role</option>
                                    @if (!$roles->isEmpty() && $roles != null )
                                        @foreach ($roles as $role)
                                            <option data-role-id="{{$role->id}}" data-role-slug="{{$role->slug}}" value="{{$role->id}}" {{ $user->roles->isEmpty() || $role->name != $userRole->name ? "" : "selected"}}>{{$role->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                      </fieldset>
                      <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Select Permissions</label>
                        <div class="col-sm-8">
                            <div id="permissions_checkbox_list">
            
                            </div>
                        </div>
                      </div>
                        @if($user->permissions->isNotEmpty())
                            @if($rolePermissions != null)
                                <div id="user_permissions_box" >
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">User Permissions</label>
                                        <div class="col-sm-8">
                                            <div id="user_permissions_checkbox_list">
                                                @foreach ($rolePermissions as $permission)
                                                    <div class="custom-control custom-checkbox">                         
                                                        <input class="custom-control-input" type="checkbox" name="permissions[]" id="{{$permission->slug}}" value="{{$permission->id}}" {{ in_array($permission->id, $userPermissions->pluck('id')->toArray() ) ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label" for="{{$permission->slug}}">{{$permission->name}}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/user">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_user_edit')

        <script>

            $(document).ready(function(){
                var permissions_box = $('#permissions_box');//initializing
                var permissions_checkbox_list = $('#permissions_checkbox_list');//initializing
                var user_permissions_box = $('#user_permissions_box');
                var user_permissions_checkbox_list = $('#user_permissions_checkbox_list');

                permissions_box.hide();//hiding box

                $('#role').on('change', function(){//change when user select a role
                    var role = $(this).find(':selected');//initializing. Take the option it selected
                    var role_id = role.data('role-id');//initializing. Using role's data to take id
                    var role_slug = role.data('role-slug')//initializing. Using role's data to take slug

                    permissions_checkbox_list.empty();//making sure box is empty or else it will flood the page with previous checkbox
                    user_permissions_box.empty();

                    $.ajax({//getting requests without loading page
                        url: '/admin/user/create',//url to do request
                        method: 'get',//type of request
                        dataType: 'json',//type of data we are sending
                        data: {//data we are getting
                            role_id: role_id,
                            role_slug: role_slug,
                        }
                    }).done(function(data){//response. what we are going to do
                        permissions_box.show();//show permission box
                        $.each(data, function(index, element){//getting each data. index and element are the parameters to bring name of slug
                            $(permissions_checkbox_list).append(//appending it to permissions checkbox list
                                '<div class="custom-control custom-checkbox">'+    //checkboxes                     
                                    '<input class="custom-control-input" type="checkbox" name="permissions[]" id="'+ element.slug +'" value="'+ element.id +'">' +
                                    '<label class="custom-control-label" for="'+ element.slug +'">'+ element.name +'</label>'+
                                '</div>'
                            );
                        });
                    });
                });
            });

        </script>

    @endsection

@endsection