@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Category</h5>

    <div class="card-body">
        <form method="POST" action="{{ route('category.update',$category->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               placeholder="Enter category name"
                               value="{{ old('title',$category->title) }}">

                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-control">
                            <option value="">--Select Gender--</option>
                            <option value="male" {{ old('gender',$category->gender) == 'male' ? 'selected' : '' }}>Men</option>
                            <option value="female" {{ old('gender',$category->gender) == 'female' ? 'selected' : '' }}>Women</option>
                        </select>

                        @error('gender')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Photo</label>

                        <input type="file"
                               class="form-control"
                               name="photo"
                               accept="image/*"
                               onchange="previewImage(event)">

                        <div id="holder" style="margin-top:15px;">
                           @if($category->photo)
                              <img src="{{ asset(is_array($category->photo) ? $category->photo[0] : $category->photo) }}"
                                  style="max-height:100px;">
                            @endif
                        </div>

                        @error('photo')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>

                        <select name="status" class="form-control">
                            <option value="active" {{ old('status',$category->status)=='active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status',$category->status)=='inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        @error('status')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="form-group">
                <label>Is Parent</label><br>

                <input type="checkbox"
                       name="is_parent"
                       id="is_parent"
                       value="1"
                       {{ old('is_parent',$category->is_parent) ? 'checked' : '' }}>
                Yes
            </div>

            <div class="form-group {{ old('is_parent',$category->is_parent) ? 'd-none' : '' }}"
                 id="parent_cat_div">

                <label>Parent Category</label>

                <select name="parent_id" class="form-control">
                    <option value="">--Select any category--</option>

                    @foreach($parent_cats as $parent_cat)
                        <option value="{{ $parent_cat->id }}"
                            {{ old('parent_id',$category->parent_id)==$parent_cat->id ? 'selected' : '' }}>
                            {{ $parent_cat->title }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="form-group">
                <label>Category Description</label>

                <textarea class="form-control"
                          name="summary"
                          rows="4">{{ old('summary',$category->summary) }}</textarea>

                @error('summary')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <button type="reset" class="btn btn-warning">Reset</button>
                <button type="submit" class="btn btn-success">Update</button>
            </div>

        </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#summary').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
</script>
<script>
  $('#is_parent').change(function(){
    var is_checked=$('#is_parent').prop('checked');
    // alert(is_checked);
    if(is_checked){
      $('#parent_cat_div').addClass('d-none');
      $('#parent_cat_div').val('');
    }
    else{
      $('#parent_cat_div').removeClass('d-none');
    }
  })
</script>
@endpush