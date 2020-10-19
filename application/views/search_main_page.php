<!DOCTYPE html>
<html lang="en">
<head>
  <title>STKI Project</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container" style="margin-top: 200px; margin-bottom: 200px;">
<center><h3><b>STKI SEARCH ENGINE</b></h3></center>
<form action="<?php echo base_url() . 'search'; ?>" method="GET">
<div class="form-group">
    <input type="text" class="form-control" placeholder="Masukkan kata kunci pencarian..." name="q" id="q" value="" required>
</div>
<div class="buton1" style="float:right;">
<button type="submit" class="btn btn-success">SEARCH</button>
</div>
<div class="buton2">
<button type="button" name="upload_file_btn" id="upload_file_btn" class="btn btn-primary">UPLOAD PDF</button>
</div>
</form>
</div>
<script type="text/javascript">
$(document).ready(function(){
$('#upload_file_btn').click(function(){
window.location.href = '<?php echo base_url() . 'upload_page'; ?>';
});
});
</script>
</body>
<footer style ="position:fixed;left:0;bottom:0;width:100%">
    <div class="foter" style="height: 50px; background-color: grey; color: white; text-align: center; padding-top: 0px;">
    Created by 17.01.53.0030 , 17.01.53.0037, 17.01.53.0071
	</div>
</footer>
</html>