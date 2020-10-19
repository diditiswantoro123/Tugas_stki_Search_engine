<!DOCTYPE html>
<html lang="en">
<head>
  <title>STKI Projek - Search Result for <?php echo $this->session->flashdata('current_query'); ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container" style="margin-top: 50px; margin-bottom: 50px;">
<center><h3><b>STKI SEARCH ENGINE</b></h3></center>
<form action="<?php echo base_url() . 'search'; ?>" method="GET">
<div class="form-group">
    <input type="text" class="form-control" placeholder="Masukkan kata kunci pencarian..." name="q" id="q" value="<?php echo $this->session->flashdata('current_query'); ?>" required>
</div>
<div class="buton1" style="float:right;">
<button type="submit" class="btn btn-success">SEARCH</button> 
</div>
<div class="buton2">
<button type="button" name="upload_file_btn" id="upload_file_btn" class="btn btn-primary">UPLOAD PDF</button>
</div>
</form>
<?php
$query_duration = $this->session->flashdata('query_duration');
?>
<h4><b>Menampilkan hasil pencarian untuk kata kunci "<?php echo $this->session->flashdata('current_query'); ?>"</b></h4><br>
<p>Query Load Time: <?php echo $query_duration . ' detik'; ?></p>
<?php
$status = $this->session->flashdata('search_notif');

if($status == "sukses")
{
	$arraynya = $this->session->flashdata('search_data_array');
	$sizeArray = sizeof($arraynya);

	if($sizeArray <= 0)
	{
		echo '<h6 style="color: red;">Tidak ada dokumen pdf ditemukan!</h6>';
	}

	else
	{
		for($loop = 0; $loop < $sizeArray; $loop++)
		{
			$current_querynya = strtolower($this->session->flashdata('current_query'));

			$explode_current_querynya = explode(" ", $current_querynya);

			$desc = $arraynya[$loop]['desc'];

			for($u = 0; $u < sizeof($explode_current_querynya); $u++)
			{
				$desc = str_replace($explode_current_querynya[$u], "<b>" . $explode_current_querynya[$u] . "</b>", $desc);
			}

			$nama_file = $arraynya[$loop]['nama_file'];

			$dokumen_id = $arraynya[$loop]['dokumen_id'];
			$file_url = $arraynya[$loop]['file_url'];

			$uploaded_at_timestamp = $arraynya[$loop]['timestamp'];
			$uploaded_at_timestamp = date("Y-m-d H:i:s", $uploaded_at_timestamp) . " WIB";

			echo '<h6 style="color: #33ccff;"><a href="' . $file_url . '">[' . $dokumen_id . '] ' . $nama_file . '</a></h6>';
			echo '<p style="margin-top: -5px; margin-bottom: -2px;"><small>Diupload pada: ' . $uploaded_at_timestamp . '</small></p>';
			echo '<p>' . $desc . '</p>';
			echo '<hr>';
		}

		//var_dump($arraynya);
	}
}

else if($status == "Tidak ada dokumen pdf ditemukan!")
{
	echo '<h6 style="color: red;">Tidak ada dokumen pdf ditemukan!</h6>';
}

else if($status == "Kata kunci kosong!")
{
	echo '<h6 style="color: red;">Kata kunci kosong!</h6>';
}

else
{
	echo '<h6 style="color: red;">Terjadi kesalahan!</h6>';
}
?>
</div>
<script type="text/javascript">
$(document).ready(function(){
$('#upload_file_btn').click(function(){
window.location.href = '<?php echo base_url() . 'upload_page'; ?>';
});
});
</script>
</body>
<footer>
    <div class="foter" style="height: 50px; background-color: grey; color: white; text-align: center; padding-top: 0px;">
    Created by 17.01.53.0030 , 17.01.53.0037, 17.01.53.0071
	</div>
</footer>
</html>