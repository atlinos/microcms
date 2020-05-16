<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="microcms.css">
    <title>MicroCMS - Home</title>
</head>
<body>
<header>
    <h1>MicroCMS</h1>
</header>

<?php foreach ($articles as $article): ?>

    <article>
        <h2><?php echo $article['title'] ?></h2>
        <p><?php echo $article['content'] ?></p>
    </article>

<?php endforeach ?>

<footer class="footer">
    <a href="https://github.com/bpesquet/OC-MicroCMS">MicroCMS</a> is a minimalistic CMS built as a showcase for
    modern PHP development.
</footer>
</body>
</html>