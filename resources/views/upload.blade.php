@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html>
<body>

<form action="{{url('upload')}}" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="image" id="image">
    <input type="hidden" name="attachedItem" id="attachedItem" value="geolocation">
    <input type="hidden" name="attachedID" id="attachedID" value="2">
    <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>
@endsection
