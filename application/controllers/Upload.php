<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
date_default_timezone_set('Asia/Jakarta');

class Upload extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
    }

    public function index()
    {
        $this->file_upload();
    }

    public function file_upload()
    {
        $config['upload_path']          = './documents/'; // DIRECTORY
        $config['allowed_types']        = 'pdf'; // ONLY FILE PDF
        $config['max_size']             = 10024; // 10 MB

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('file_dokumen'))
        {
            $error = strip_tags($this->upload->display_errors());
            $this->session->set_flashdata('upload_message_error', $error);
            redirect(base_url() . 'upload_page');
            //$this->load->view('upload_page', $error);
        }
        
        else
        {
            $data = $this->upload->data();

            $arrayFileInfo = array('file_name'=>$data['file_name'], 'file_type'=>$data['file_type'], 'raw_name'=>$data['raw_name'], 'orig_name'=>$data['orig_name'], 'client_name'=>$data['client_name'], 'file_ext'=>$data['file_ext'], 'file_size'=>floatval($data['file_size']));

            $pdfText = $this->parsePDF('./documents/' . $data['file_name'], $arrayFileInfo);

            $this->session->set_flashdata('upload_message', $pdfText);
            redirect(base_url() . 'upload_page');
            //$this->load->view('upload_page');

            //redirect('/upload_page', $pdfText);
        }
    }

    public function parsePDF($docLocation, $getArrayFileInfo)
    {
        $getDocLocation = $docLocation;
        
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($getDocLocation);
        
        $details  = $pdf->getDetails();

        //var_dump($details);

		$title = $details['Title'];
		$author = $details['Author'];
		$creator = $details['Creator'];
		$CreationDate = $details['CreationDate'];
		$ModDate = $details['ModDate'];
		$Producer = $details['Producer'];
		$Pages = (int)$details['Pages'];
        $namafile = $getArrayFileInfo['file_name'];
        $filesize = floatval($getArrayFileInfo['file_size']);
        $fileext = $getArrayFileInfo['file_ext'];

		$PdfText = $pdf->getText();

		//DOKUMEN ID
		$bytes_data = openssl_random_pseudo_bytes(32);
        $newHash = bin2hex($bytes_data);
        $timestamp = time();
        $dokumenId = hash('crc32b', $newHash . $timestamp . $getArrayFileInfo['file_name']);
        
        $TokenizingResult = str_replace(array("\t", "\r", "\n", "\r\n", "'", "-", ")", "(", "\"", "/", "=", ".", ",", ":", ";", "!", "?", ">", "<"), ' ', $PdfText);
		$lowerCase = $this->lowerCaseText($TokenizingResult);
		$tokenizingWord = $this->tokenisasi($lowerCase);
		$stemmingPerWordText = $this->stemmingKataDasar($tokenizingWord, $dokumenId);

		$resultPdfText = array('file_name'=>$namafile, 'file_size'=>$filesize, 'file_ext'=>$fileext, 'title'=>$title, 'author'=>$author, 'creator'=>$creator, 'CreationDate'=>$CreationDate, 'ModDate'=>$ModDate, 'Producer'=>$Producer, 'Pages'=>$Pages, 'lowerCaseFoldingText'=>$lowerCase, 'tokenizing'=>$tokenizingWord, 'stemming'=>$stemmingPerWordText, 'dokumen_id_hash_name'=>$dokumenId);

		foreach($resultPdfText as $key => $value)
		{
		    if(is_null($value))
		    {
		         $resultPdfText[$key] = "";
    		}
        }

        $insertFileToDB = "INSERT INTO dokumen(nama_file, dokumen_id, file_url, file_format, timestamp, content) VALUES(?, ?, ?, ?, ?, ?)";
        $doInsertFileToDB = $this->db->query($insertFileToDB, array($namafile, $dokumenId, $namafile, $fileext, $timestamp, $lowerCase));

        return $resultPdfText;
    }

    public function lowerCaseText($datanya)
    {
        $getDatanya = $datanya;
        $getDatanya = strtolower($getDatanya);

        return $getDatanya;
    }

    public function tokenisasi($datanya)
    {
        $getTokenisasi = $datanya;

        $filter = explode(" ", $getTokenisasi); //proses awal tokenisasi, pisah dengan spasi

        //STOPWORD REMOVAL
        $astoplist = array("a", "about", "above", "acara", "across", "ada", "adalah", "adanya", "after", "afterwards", "again", "against", "agar", "akan", "akhir", "akhirnya", "akibat", "aku", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "among", "amongst", "amoungst", "amount", "an", "and", "anda", "another", "antara", "any", "anyhow", "anyone", "anything", "anyway", "anywhere", "apa", "apakah", "apalagi", "are", "around", "as", "asal", "at", "atas", "atau", "awal", "b", "back", "badan", "bagaimana", "bagi", "bagian", "bahkan", "bahwa", "baik", "banyak", "barang", "barat", "baru", "bawah", "be", "beberapa", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begitu", "behind", "being", "belakang", "below", "belum", "benar", "bentuk", "berada", "berarti", "berat", "berbagai", "berdasarkan", "berjalan", "berlangsung", "bersama", "bertemu", "besar", "beside", "besides", "between", "beyond", "biasa", "biasanya", "bila", "bill", "bisa", "both", "bottom", "bukan", "bulan", "but", "by", "call", "can", "cannot", "cant", "cara", "co", "con", "could", "couldnt", "cry", "cukup", "dalam", "dan", "dapat", "dari", "datang", "de", "dekat", "demikian", "dengan", "depan", "describe", "detail", "di", "dia", "diduga", "digunakan", "dilakukan", "diri", "dirinya", "ditemukan", "do", "done", "down", "dua", "due", "dulu", "during", "each", "eg", "eight", "either", "eleven", "else", "elsewhere", "empat", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "gedung", "get", "give", "go", "had", "hal", "hampir", "hanya", "hari", "harus", "has", "hasil", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "hidup", "him", "himself", "hingga", "his", "how", "however", "hubungan", "hundred", "ia", "ie", "if", "ikut", "in", "inc", "indeed", "ingin", "ini", "interest", "into", "is", "it", "its", "itself", "itu", "jadi", "jalan", "jangan", "jauh", "jelas", "jenis", "jika", "juga", "jumat", "jumlah", "juni", "justru", "juta", "kalau", "kali", "kami", "kamis", "karena", "kata", "katanya", "ke", "kebutuhan", "kecil", "kedua", "keep", "kegiatan", "kehidupan", "kejadian", "keluar", "kembali", "kemudian", "kemungkinan", "kepada", "keputusan", "kerja", "kesempatan", "keterangan", "ketiga", "ketika", "khusus", "kini", "kita", "kondisi", "kurang", "lagi", "lain", "lainnya", "lalu", "lama", "langsung", "lanjut", "last", "latter", "latterly", "least", "lebih", "less", "lewat", "lima", "ltd", "luar", "made", "maka", "mampu", "mana", "mantan", "many", "masa", "masalah", "masih", "masing-masing", "masuk", "mau", "maupun", "may", "me", "meanwhile", "melakukan", "melalui", "melihat", "memang", "membantu", "membawa", "memberi", "memberikan", "membuat", "memiliki", "meminta", "mempunyai", "mencapai", "mencari", "mendapat", "mendapatkan", "menerima", "mengaku", "mengalami", "mengambil", "mengatakan", "mengenai", "mengetahui", "menggunakan", "menghadapi", "meningkatkan", "menjadi", "menjalani", "menjelaskan", "menunjukkan", "menurut", "menyatakan", "menyebabkan", "menyebutkan", "merasa", "mereka", "merupakan", "meski", "might", "milik", "mill", "mine", "minggu", "misalnya", "more", "moreover", "most", "mostly", "move", "much", "mulai", "muncul", "mungkin", "must", "my", "myself", "nama", "name", "namely", "namun", "nanti", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "oleh", "on", "once", "one", "only", "onto", "or", "orang", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own", "pada", "padahal", "pagi", "paling", "panjang", "para", "part", "pasti", "pekan", "penggunaan", "penting", "per", "perhaps", "perlu", "pernah", "persen", "pertama", "pihak", "please", "posisi", "program", "proses", "pula", "pun", "punya", "put", "rabu", "rasa", "rather", "re", "ribu", "ruang", "saat", "sabtu", "saja", "salah", "sama", "same", "sampai", "sangat", "satu", "saya", "sebab", "sebagai", "sebagian", "sebanyak", "sebelum", "sebelumnya", "sebenarnya", "sebesar", "sebuah", "secara", "sedang", "sedangkan", "sedikit", "see", "seem", "seemed", "seeming", "seems", "segera", "sehingga", "sejak", "sejumlah", "sekali", "sekarang", "sekitar", "selain", "selalu", "selama", "selasa", "selatan", "seluruh", "semakin", "sementara", "sempat", "semua", "sendiri", "senin", "seorang", "seperti", "sering", "serious", "serta", "sesuai", "setelah", "setiap", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "suatu", "such", "sudah", "sumber", "system", "tahu", "tahun", "tak", "take", "tampil", "tanggal", "tanpa", "tapi", "telah", "teman", "tempat", "ten", "tengah", "tentang", "tentu", "terakhir", "terhadap", "terjadi", "terkait", "terlalu", "terlihat", "termasuk", "ternyata", "tersebut", "terus", "terutama", "tetapi", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "tidak", "tiga", "tinggal", "tinggi", "tingkat", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "ujar", "umum", "un", "under", "until", "untuk", "up", "upaya", "upon", "us", "usai", "utama", "utara", "very", "via", "waktu", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "wib", "will", "with", "within", "without", "would", "ya", "yaitu", "yakni", "yang", "yet", "you", "your", "yours", "yourself", "yourselves");

        $jumlahKata = count($filter);

        //HAPUS STOPWORD
        $tokenisasiArray = array();
		for($loop = 0; $loop < $jumlahKata; $loop++)
		{
			if(!in_array($filter[$loop], $astoplist) && $filter[$loop] != "")
			{
				array_push($tokenisasiArray, $filter[$loop]);
			}
		}

		$tokenisasiArrayJadi = array('tokenizingResult'=>$tokenisasiArray);

		return $tokenisasiArrayJadi;
    }

    public function stemmingKataDasar($stemmingData, $getDokumenId)
    {
        $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
        $stemmer  = $stemmerFactory->createStemmer();
        
        $getStemmingData = $stemmingData;
        $getCurrentDokumenId = $getDokumenId;
        
        $arrayStemmingKata = array();

        for($cobaUlangi = 0; $cobaUlangi < sizeof($getStemmingData['tokenizingResult']); $cobaUlangi++)
		{
			$cleanWord = preg_replace("/[^A-Za-z0-9-]/", ' ', $getStemmingData['tokenizingResult'][$cobaUlangi]); // Bersihkan karakter spesial yang masih ada

			$outputStem = $stemmer->stem($cleanWord);

			$arrayStemmingKata2 = [
									'token'=>$cleanWord,
									'tokenstem'=>$outputStem
									];

			array_push($arrayStemmingKata, $arrayStemmingKata2);

			$updateToken = "INSERT INTO token(dokumen_id, token, tokenstem) VALUES(?, ?, ?)";
			$doUpdateToken = $this->db->query($updateToken, array($getCurrentDokumenId, $cleanWord, $outputStem));
		}

		return $arrayStemmingKata;
    }
}
?>