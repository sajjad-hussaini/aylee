@extends('backend.layouts.master')

@section('main-content')

<div class="card">
  <h5 class="card-header">Edit Product</h5>
  <div class="card-body">
    <form method="post" action="{{route('product.update',$product->id)}}">
      @csrf
      @method('PATCH')

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="gender">Gender <span class="text-danger">*</span></label>
            <select name="gender" id="gender" class="form-control">
              <option value="">--Select any gender--</option>
              <option value="male" {{($product->gender=='male') ? 'selected' : ''}}>Men</option>
              <option value="female" {{($product->gender=='female') ? 'selected' : ''}}>Women</option>
            </select>
          </div>
        </div>

        <div class="col-md-8">
          <div class="form-group">
            <label for="cat_id">Category <span class="text-danger">*</span></label>
            <select name="cat_id" id="cat_id" class="form-control">
              <option value="">--Select any category--</option>
              @foreach($categories as $key=>$cat_data)
                <option value='{{$cat_data->id}}' {{(($product->cat_id==$cat_data->id)? 'selected' : '')}}>{{$cat_data->title}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      @php
        $sub_cat_info = DB::table('categories')->select('title')->where('id',$product->child_cat_id)->get();
      @endphp

      <div class="form-group {{(($product->child_cat_id)? '' : 'd-none')}}" id="child_cat_div">
        <label for="child_cat_id">Sub Category</label>
        <select name="child_cat_id" id="child_cat_id" class="form-control">
          <option value="">--Select any sub category--</option>
          @if($product->child_cat_id)
            <option value="{{$product->child_cat_id}}" selected>{{ $sub_cat_info[0]->title ?? '' }}</option>
          @endif
        </select>
      </div>

      <div class="form-group">
        <label for="inputTitle" class="col-form-label">Article Title <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter title" value="{{$product->title}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="is_featured">Is Featured</label><br>
        <input type="checkbox" name='is_featured' id='is_featured' value='1' {{(($product->is_featured) ? 'checked' : '')}}> Yes
      </div>

      <div class="form-group">
        <label class="col-form-label">Product Section</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="section" id="section_focus" value="focus" {{ $product->section === 'focus' ? 'checked' : '' }}>
          <label class="form-check-label" for="section_focus">Categories in Focus</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="section" id="section_must_haves" value="must_haves" {{ $product->section === 'must_haves' ? 'checked' : '' }}>
          <label class="form-check-label" for="section_must_haves">Must-Haves</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="section" id="section_sale_essentials" value="sale_essentials" {{ $product->section === 'sale_essentials' ? 'checked' : '' }}>
          <label class="form-check-label" for="section_sale_essentials">Sale essentials</label>
        </div>
        <small class="form-text text-muted">If no section is selected, the product will be added to the common section.</small>
        @error('section')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
            <input id="price" type="number" name="price" placeholder="Enter price" value="{{$product->price}}" class="form-control">
            @error('price')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="discount" class="col-form-label">Discount(%)</label>
            <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount" value="{{$product->discount}}" class="form-control">
            @error('discount')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-6">
          <div class="form-group">
            <label for="size">Size</label>
            <select name="size[]" class="form-control selectpicker" multiple data-live-search="true">
              @php
                $sizeData = $items->isNotEmpty() ? explode(',', $items->first()->size) : [];
              @endphp
              <option value="S" {{ in_array("S", $sizeData) ? 'selected' : '' }}>Small (S)</option>
              <option value="M" {{ in_array("M", $sizeData) ? 'selected' : '' }}>Medium (M)</option>
              <option value="L" {{ in_array("L", $sizeData) ? 'selected' : '' }}>Large (L)</option>
              <option value="XL" {{ in_array("XL", $sizeData) ? 'selected' : '' }}>Extra Large (XL)</option>
            </select>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="colors">Colors</label>
            <select name="colors[]" class="form-control selectpicker" multiple data-live-search="true">
              @foreach($brands as $brand)
                <option value="{{$brand->id}}" {{ in_array($brand->id, $product->colors ?? []) ? 'selected' : '' }}>{{$brand->title}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="stock">Quantity <span class="text-danger">*</span></label>
        <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity" value="{{$product->stock}}" class="form-control">
        @error('stock')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="col-form-label">Photos <span class="text-danger">*</span></label>
        <div id="product-image-dropzone" class="dropzone border rounded p-2"></div>
        @error('photo')
        <span class="text-danger">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
          <option value="active" {{(($product->status=='active')? 'selected' : '')}}>Active</option>
          <option value="inactive" {{(($product->status=='inactive')? 'selected' : '')}}>Inactive</option>
        </select>
        @error('status')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
        <textarea class="form-control" id="summary" name="summary">{{$product->summary}}</textarea>
        @error('summary')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="description" class="col-form-label">Description</label>
        <textarea class="form-control" id="description" name="description">{{$product->description}}</textarea>
        @error('description')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div id="temp-paths"></div>
      <div class="form-group mb-3">
        <button class="btn btn-success" type="submit">Update</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#summary').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
    $(document).ready(function() {
      $('#description').summernote({
        placeholder: "Write detail Description.....",
          tabsize: 2,
          height: 150
      });
    });
</script>

<script>
  var  child_cat_id='{{$product->child_cat_id}}';
        // alert(child_cat_id);
        $('#cat_id').change(function(){
            var cat_id=$(this).val();

            if(cat_id !=null){
                // ajax call
                $.ajax({
                    url:"/admin/category/"+cat_id+"/child",
                    type:"POST",
                    data:{
                        _token:"{{csrf_token()}}"
                    },
                    success:function(response){
                        if(typeof(response)!='object'){
                            response=$.parseJSON(response);
                        }
                        var html_option="<option value=''>--Select any one--</option>";
                        if(response.status){
                            var data=response.data;
                            if(response.data){
                                $('#child_cat_div').removeClass('d-none');
                                $.each(data,function(id,title){
                                    html_option += "<option value='"+id+"' "+(child_cat_id==id ? 'selected ' : '')+">"+title+"</option>";
                                });
                            }
                            else{
                                console.log('no response data');
                            }
                        }
                        else{
                            $('#child_cat_div').addClass('d-none');
                        }
                        $('#child_cat_id').html(html_option);

                    }
                });
            }
            else{

            }

        });
        if(child_cat_id!=null){
            $('#cat_id').change();
        }
   $('#gender').change(function() {
    var gender = $(this).val();
    // alert(gender);
    if (gender != null) {
      // Ajax call
      $.ajax({
        url: "/admin/category/parent",
        data: {
          _token: "{{csrf_token()}}",
          gender: gender
        },
        type: "POST",
        success: function(response) {
          if (typeof(response) != 'object') {
            response = $.parseJSON(response)
          }
          // console.log(response);
          var _parent = "<option value=''>--Select category--</option>"
          if (response.status) {
            var data = response.data;
            if (response.data && data.length > 0) {
              $('#cat_div').removeClass('d-none');
            $.each(data, function(index, item) {
                _parent += "<option value='" + item.id + "'>" + item.title + "</option>";
            });
            } else {
                _parent += "<option value=''>No category found</option>";
            }
          } else {
             _parent += "<option value=''>No category found</option>";
          }
          $('#cat_id').html(_parent);
        }
      });
    } else {}
  })
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script>
Dropzone.autoDiscover = false;

const myDropzone = new Dropzone("#product-image-dropzone", {
    url: "/admin/products/images/temp", 
    paramName: "file",
    maxFilesize: 2,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    init: function () {
        @isset($product)
            @foreach($product->media as $image)
                let mockFile{{ $image->id }} = { name: "image-{{ $image->id }}", size: 1234, id: {{ $image->id }} };
                this.displayExistingFile(mockFile{{ $image->id }}, "{{ asset($image->path) }}");
            @endforeach
        @endisset

        this.on("removedfile", function (file) {
            if (file.id) {
                fetch(`/products/images/${file.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
            }
        });

        this.on("success", function (file, response) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'temp_images[]';
            input.value = response.temp_path;
            document.getElementById('temp-paths').appendChild(input);
        });
    }
});
</script>
@endpush