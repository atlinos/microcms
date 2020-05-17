<?php

function getArticles()
{
    $bdd = new PDO('mysql:host=localhost;dbname=microcms;charset=utf8', 'microcms_user', 'secret');
    $articles = $bdd->query('select * from article order by id desc');

    return $articles;
}