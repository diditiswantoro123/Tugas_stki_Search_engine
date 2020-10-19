<!DOCTYPE html>
<html lang="en">
<head>
  <title>UPLOAD</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container" style="margin-top: 100px; margin-bottom: 100px;">
  <div class="Logoku" style="text-align:center;">
  <h2>Upload File PDF</h2>
  </div>
  <form action="upload" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="file_dokumen">Cari File PDF dibawah ini : </label>
      <input type="file" class="form-control" id="file_dokumen" placeholder="Enter email" name="file_dokumen" required>
    </div>
    <div class="buton1" style="float:right;">
    <button type="submit" class="btn btn-primary">UPLOAD</button>
    </div>
    <div class="buton2">
    <button type="button" name="cari_pdf_btn" id="cari_pdf_btn" class="btn btn-success">SEARCH</button>
    </div>
  </form><br>
  <?php
  if($this->session->flashdata('upload_message_error') != "")
  {
    echo '<h3>' . $this->session->flashdata('upload_message_error') . '</h3>';
  }

  if($this->session->flashdata('upload_message') != "")
  {
    $getArraynya = $this->session->flashdata('upload_message');
    //var_dump($getArraynya);

    $titlenya = $getArraynya['title'][0];
    $authornya = $getArraynya['author'][0];
    $producernya = $getArraynya['producer'][0];
    $creatornya = $getArraynya['creator'][0];

    if($titlenya == "")
    {
      $titlenya = "-";
    }

    if($authornya == "")
    {
      $authornya = "-";
    }

    if($producernya == "")
    {
      $producernya = "-";
    }

    if($creatornya == "")
    {
      $creatornya = "-";
    }

    echo '<h3>Hasil Output Upload:</h3>';

    echo '<div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Format</th>
          <th>Ukuran</th>
          <th>Judul</th>
          <th>Author</th>
          <th>Producer</th>
          <th>Creator</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>' . $getArraynya['file_name'] . '</td>
          <td>' . $getArraynya['file_ext'] . '</td>
          <td>' . $getArraynya['file_size'] . ' KB' . '</td>
          <td>' . $titlenya . '</td>
          <td>' . $authornya . '</td>
          <td>' . $producernya . '</td>
          <td>' . $creatornya . '</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>';
  }
  ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
$('#cari_pdf_btn').click(function(){
window.location.href = '<?php echo base_url(); ?>';
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