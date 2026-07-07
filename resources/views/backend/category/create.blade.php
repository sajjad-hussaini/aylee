@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Category</h5>
    <div class="card-body">
      <form method="post" action="{{route('category.store')}}" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label for="inputTitle" class="col-form-label">Category Name <span class="text-danger">*</span></label>
                <input id="inputTitle" type="text" name="title" placeholder="Enter category name"  value="{{old('title')}}" class="form-control">
                @error('title')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="gender" class="col-form-label">Gender <span class="text-danger">*</span></label>
              <select name="gender" class="form-control">
                <option value="">--Select Gender--</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Men</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Women</option>
              </select>
              @error('gender')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
                <label for="thumbnail" class="col-form-label">Photo</label>

                <input
                    id="thumbnail"
                    class="form-control"
                    type="file"
                    name="photo"
                    accept="image/*"
                    onchange="previewImage(event)"
                >

                <div id="holder" style="margin-top:15px;">
                    @if(isset($category) && $category->photo)
                        <img src="{{ asset('storage/' . $category->photo) }}" style="max-height:100px;">
                    @endif
                </div>

                @error('photo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
              <select name="status" class="form-control">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
              </select>
              @error('status')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>
          </div>
        </div>

        
        <div class="form-group">
          <label for="is_parent">Is Parent</label><br>
          <input type="checkbox" name='is_parent' id='is_parent' value='1' checked> Yes                        
        </div>

        <div class="form-group d-none" id='parent_cat_div'>
          <label for="parent_id">Parent Category</label>
          <select name="parent_id" class="form-control">
              <option value="">--Select any category--</option>
              @foreach($parent_cats as $key=>$parent_cat)
                  <option value='{{$parent_cat->id}}'>{{$parent_cat->title}}</option>
              @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">Category Description</label>
          <textarea class="form-control" id="summary" name="summary">{{old('summary')}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Submit</button>
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
    $(document).ready(function() {
      $('#summary').summernote({
        placeholder: "Write short description.....",
          tabsize: 2,
          height: 120
      });
    });
</script>

<script>
function previewImage(event) {
    const holder = document.getElementById('holder');
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            holder.innerHTML = `<img src="${e.target.result}" style="max-height:100px;">`;
        };
        reader.readAsDataURL(file);
    }
}
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