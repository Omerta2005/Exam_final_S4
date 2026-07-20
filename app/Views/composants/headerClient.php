<meta charset="UTF-8">

<meta name="viewport" 
      content="width=device-width, initial-scale=1.0">


<title>
<?= $title ?? 'Mobile Money' ?>
</title>


<script>
if(localStorage.getItem('daynight-theme') === 'carbon'){
    document.documentElement.classList.add('carbon');
}
</script>
<link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">