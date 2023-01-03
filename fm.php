<?php  
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
mb_internal_encoding("UTF-8");  
 
//  *****    DOWNLOAD ***** 
        if (isset($_GET['download'])) {  
            $file = $_GET['download'];
                
            if (file_exists($file)) { // если файл существует, считываю его параметры и скачиваю
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            } else {}
        } else {}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
        <title>File Manager</title>
        <style>
            body {font-family: 'Play', sans-serif; font-size: 16px; padding: 30px;}
            a {text-decoration: none; color: #252525;}
            a:hover {color: blue}
            h2 {text-align: center;}
            .form-upload {float:right; margin-bottom: 30px;}
            .form-rename {float:left; margin-bottom: 30px;}
            table {width: 100%; text-align:left;}
            table, th, td {border: 0.5px solid gray; border-collapse: collapse; padding: 5px;}
            img {width: 20px; height:auto; margin: 0 7px;} 
            textarea {min-height: 250px; resize: vertical; padding: 10px; width: 98%;}
            .sucsess {border: 1px solid gray; background: #adff7a; padding: 5px;}
            .error {border: 1px solid gray; background: #f59393; padding: 5px;}
            #form-text-edit {margin-top: 30px; display: flex; flex-direction: column; justify-content: center; gap: 15px;} 
            .submit, .label-upload, .label-form-edit {border: 1px solid black; border-radius: 3px; padding: 6px; font-size: 16px; background: #e6eced; cursor: pointer; font-family: 'Play', sans-serif; font-size: 16px;}
            .form-upload input[type=file],  #form-text-edit input[type=submit] {display: none;} 
            .label-upload {margin-right: 15px; display: inline-block; width: 100px; height: 19px; color: black;}
            .rename-input {padding: 8px;} 
            .label-form-edit {width: 100px; text-align: center; display: inline-block; height: 19px; color: black;}
            .rename-input-hidden {width: 0; padding: 0; margin: 0; visibility: hidden;}
            .hidden  {visibility: hidden; padding: 0; height: 0; padding: 0; cursor: pointer;}
            .edit-input {display:none;}
            .edit-label {cursor: pointer;}
            .edit-label:hover {color: blue;}
            .edit-form {display: inline;}     
        </style>
    </head>
    <body>
<?php  
    
        $dir = $_SERVER['DOCUMENT_ROOT'].'/';
            if (!is_dir( $dir )) { 
                $dir = dirname(__FILE__);
            } else {}
        $path = (isset($_GET['path'])) ? $_GET['path'] : $dir; //если есть гет-запрос, то путь path = из ссылки, иначе path = корневая папка
   
        //  ******* Messages ***********
            if (isset($_GET['msg'])) {
                if ($_GET['msg'] === 'delFolder') {
                echo "<div class='sucsess'>Папка удалена</div>";
                } else if ($_GET['msg'] === 'delFile') {
                echo "<div class='sucsess'>Файл удален</div>";
                } else if ($_GET['msg'] === 'addFile') {
                    echo '<div class ="sucsess">Файл добавлен</div>';
                } else if ($_GET['msg'] === 'renameFile')  {
                    echo "<div class='sucsess'>Файл переименован</div>";
                } else  if ($_GET['msg'] === 'edit')  {
                    echo "<div class='sucsess'>Файл изменен</div>";  
                } else {} 
            } 

        //  ******* UPLOAD ***********

        if (isset($_FILES['file'])) {   //если загружен файл input file
            if (isset($_POST['submitUpload']) and !is_uploaded_file($_FILES['file']['tmp_name']))  {
                echo '<div class = "error">Выбирите файл</div>'; // нажата submit , но файл НЕ загружен
            } 
            else if (isset($_POST['submitUpload']) and is_uploaded_file($_FILES['file']['tmp_name'])) { // все ок
                $fileName = basename($_FILES['file']['name']); //получаю последний элемент пути - имя файла
                echo "это имя загружаемого файла -  ". $fileName.'<br>';
                $fileName = mb_convert_encoding($fileName, "windows-1251"); // если загружен файл, названный кириллицей
              
                echo "это путь + имя загружаемого файла -  ". $path.$fileName.'<br>';
                move_uploaded_file($_FILES['file']['tmp_name'], $path.$fileName); //загружаю файл по указанному пути

               // header('Location: ?path='.$path.'&msg=addFile');
            } else {
                    echo 'Ошибка загрузки';
            }
        } else {}

        // ********** RENAME **********
        if (isset($_GET['newName']) && isset($_GET['submitRename']) && isset($_GET['oldPath']) && isset($_GET['oldName'])) {

       
            $newPath = urldecode(($_GET['oldPath']));  // путь, где будет лежать файл с новым именем = директории старого файла
            $old = urldecode($_GET['oldName']); // имя старого файла из инпута 
            $newName = mb_convert_encoding($_GET['newName'], "windows-1251"); //преобразую кодировку нового имени если пользователь переименовал файл кириллицей

                if (is_file($newPath.$old)) {
                 
                    if (!strpos($_GET['newName'], pathinfo($old, PATHINFO_EXTENSION))) {
                        $newName = $newName.'.'.pathinfo($old, PATHINFO_EXTENSION); //к новому имени файла, который будет указан в инпуте пользователем, будет добавляться расширение старого файла
                    } else {}

                } else {}

            rename($newPath.'/'.$old, $newPath.'/'. $newName); // переименование с помощью встроенной ф-ии
            header('Location: ?path='.$newPath.'/&msg=renameFile'); //перезагружаю страницу чтобы не было ошибки readdir при перезагрузке
        } else {}

    ?>       <div class="container"> 
                <h2>Файловый менеджер</h2> 
               
    <?php 
            // ********** RENAME FORM**********
            if (isset($_GET['rename'])) { // если нажат submit "переименовать файл/папку"
             
                $renamePath =  urlencode(dirname($_GET['rename'])).'/'; // путь к файлу/папке тут получаю изначально декодированным т.к. get
                $old = basename($_GET['rename']); 
       echo $renamePath.$old  ;
    ?> 
        <form class="form-rename" method="get" enctype="multipart/form-data"> 
            <input class="rename-input-hidden" type="text" name="path" value="<?=urlencode($path)?>">
            <br><span>Переименовать файл/папку: <b><?=iconv('cp1251','UTF-8//IGNORE', $old)?></b></span><br> 
            <input class="rename-input-hidden" type="text" name="oldName" value="<?=urlencode($old)?>">
            <input class="rename-input-hidden" type="text" name="oldPath" value="<?=$renamePath?>">
            <br><span>Введите новое имя:</span><br>
            <input class="rename-input" type="text" name="newName" placeholder="Введите имя" required value="<?=iconv('cp1251','UTF-8//IGNORE', $old)?>">
            <input class="submit" type="submit" name="submitRename" value="ОК">
        </form>
    <?php   
        } else {}
     
        // ********** DELETE **********
        if (isset($_GET['del'])) {  
            if (is_file($_GET['del'])) {
                unlink($_GET['del']);
                header('Location: ?path='.$path.'&msg=delFile');
            } else if (is_dir($_GET['del'])) { 
                delete_dir($_GET['del']); 
                header('Location: ?path='.$path.'&msg=delFolder');
            }
        } else {}

        // ********** EDIT **********                                                     
        if (isset($_POST['submitFileEdit'])) { // если нажата submit в форме
            $file = $_POST['submitFileEdit']; //  передаю путь файла из value формы
            $file = urldecode($file); //декодирую строку,т.к. при post не декодируется, чтобы не было ошибки считывания содержимого файла
            file_put_contents($file, null); // обнуляю содержимое файла
            file_put_contents($file, $_POST['newText']);  //вношу новое содержание
            header('Location: ?path='.$path.'&msg=edit');  
        } else {}

    ?>
                <form class="form-upload" method="post" enctype="multipart/form-data">
                    <label class="label-upload">Обзор...
                        <input type="file" name="file">
                    </label>
                    <input class="submit" type="submit" name="submitUpload" value="Загрузить в текущую папку">
                </form>

            <table> <!-- шапка таблицы и вторая строка -->
                
                <tr>
                    <th>Имя файла или папки</th>
                    <th width="15%">Размер</th>
                    <th width="45%">Действия</th>
                </tr>
                <tr>
                    <td>
                        <a href = "?path=<?=$dir?>">  <!-- вернуться в корень -->
                        <?=showArrow()?>.</a>                    
                    </td>
                    <td></td> 
                    <td></td>
                </tr> 
                <tr>
                    <td> 
                        <a href = "?path=<?=urlencode(dirname($path, 1))?>/"><!-- на папку выше подняться -->
                        <?=showArrow()?>..</a> 
                    </td> 
                    <td></td> 
                    <td></td>
                </tr> 
                <!-- последующие table row отображаю в цикле -->
    <?php

        if (is_dir($path)) {
            if ($handle= opendir($path)) {
                while (($file = readdir($handle)) !== false) {
                  //  echo $path.$file."<br>";
                    if (is_dir($path.$file) && $file !== '..' && $file !== '.') { //если файл путь то вывожу как папку          
    ?> 
                                <tr>
                                    <td>
                        <?=showFolder();?>
                                        <a href="?path=<?=urlencode($path).urlencode($file)?>/"> <!-- ссылка каталога, делаю кодирование для path и для file - последней открытой папки, при кодировании названной кириллицей папки ее содержимое становится видимым, при открытии папки в урл кодировка вместо ромбов  -->
    <?php              //для нормального отображения названий папок с кириллицей             
                        $showFolder = iconv("windows-1251", "UTF-8", $file); 
                        echo  $showFolder;           
    ?> 
                                        </a>
                                    </td>
                                    <td>
                                        --- <!-- не указываю размер папки-->
                                    </td>
                                    <td width="35%">  <!-- кодирую урлы -->
                                    <a href="?path=<?=urlencode($path)?>&rename=<?=urlencode($path).urlencode($file)?>/">Переименовать</a>&nbsp;&nbsp;&nbsp; 
                                    <a href="?path=<?=urlencode($path)?>&del=<?=urlencode($path).urlencode($file)?>/">Удалить</a>&nbsp;&nbsp;&nbsp;   
                                    </td>
                                </tr>
    <?php 
                    } else {}
                }
            } else {}
        

            if ($handle= opendir($path)) {
                while (($file = readdir($handle)) !== false) {
                    
                    if (is_file($path.$file)) { 
                        $fileSize = filesize($path.'/'.$file);  // определяю размер файла с пом.встроенной ф-ии и условия для отображения ед.измерения размера
                            if ($fileSize < 1024){ 
                                $fileSize = $fileSize.' б'; 
                            } 
                            else if ($fileSize >= 1024 && $fileSize < 1024**2){ 
                                $fileSize = number_format(($fileSize/1024), 2, '.',' ').' кб'; 
                            } 
                                else if ($fileSize >= 1024**2 && $fileSize<1024**3){ 
                                $fileSize = number_format(($fileSize/1024**2), 2, '.',' ').' мб'; 
                            } else {
                                $fileSize = number_format(($fileSize/1024**3), 2, '.',' ').' гб'; 
                            }
    ?>
            <tr>
                <td> 
    <?php
                    echo showFile(); // отображаю иконку
                    $showFile = iconv("windows-1251", "UTF-8", $file); // создаю отдельную переменную, чтобы вывести в списке таблицы русские названия если будут
                    echo  $showFile;                          
    ?>
                </td>
                <td>
                    <?=$fileSize?>
                </td>
                <td width="35%">
                    <a href="?path=<?=urlencode($path)?>&rename=<?=urlencode($path).urlencode($file)?>">Переименовать</a>&nbsp;&nbsp;&nbsp; 
                    <a href="?path=<?=urlencode($path)?>&del=<?=urlencode($path).urlencode($file)?>">Удалить</a>&nbsp;&nbsp;&nbsp; 
                    <a href="?path=<?=urlencode($path)?>&download=<?=urlencode($path).urlencode($file)?>">Скачать</a>&nbsp;&nbsp;&nbsp; 
    <?php 
                    $extensions = ['doc', 'docx', 'txt', 'php', 'html', 'rtf']; // отображаю возможность редактировать только текстовые файлы
                    $extention = pathinfo(urlencode($path).urlencode($file), PATHINFO_EXTENSION); //определяю расширение файла
                    if (in_array($extention, $extensions)) { // если файл имеет расширение равное какому-нибудь из массива, то вывожу редактирование:
    ?>
                    <form class="edit-form" method="post" action="" enctype="multipart/form-data">
                        <label class="edit-label">Редактировать 
                            <input class="edit-input" type="submit" name="edit" value="<?=urlencode($path).urlencode($file)?>" />
                        </label> 
                    <form>
    <?php               
                    } else {}
    ?>
                </td>
            </tr>
    <?php  
                
                    } else {}
                }
            } else {}
        } else {}
        
    ?>
            </table>
    <?php   
            // ********** EDIT FORM ***********
            if (isset($_POST['edit'])) { 
            $file = $_POST['edit']; 
            $current = file_get_contents(urldecode($file)); //декодирую путь, чтобы получить содержимое файла в textarea, которое нужно изменить
    ?>
        <form id="form-text-edit" method="post" enctype="multipart/form-data">    
            <input type="text" style="visibility: hidden;" name="path" value="<?=$path?>">
            <textarea name="newText"><?=$current?></textarea><br>
            <label class="label-form-edit">Сохранить
                <input class="submit hidden" type="submit" name="submitFileEdit" value="<?=$file?>" />
            </label>      
        </form>
    <?php } else {}

    function delete_dir($dir) {
        if (is_dir($dir))
            $handle = opendir($dir);
        if (!$handle)
            return false;

            while($file = readdir($handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dir."/".$file)) {
                        unlink($dir."/".$file);
                    } else {
                    delete_dir($dir.'/'.$file);  
                    }  
                }
            }
        closedir($handle);
        rmdir($dir);
        return true;
    }

    function showFile() {
        return '<img width="50" height="50" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjY0NzE4RDA1Rjk0MTFFQTk5MEZBNjFCODRFOUI4MTEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjY0NzE4RDE1Rjk0MTFFQTk5MEZBNjFCODRFOUI4MTEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo2NjQ3MThDRTVGOTQxMUVBOTkwRkE2MUI4NEU5QjgxMSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo2NjQ3MThDRjVGOTQxMUVBOTkwRkE2MUI4NEU5QjgxMSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgxtF8gAAAPgSURBVHja7JppaNRAFMeTVnuJouCFWrEIHnjgB1vE+0A/iCL6SSyIioJSP4giFI+CFjzqgUj9UkXqia0KiiIKVdRiq9UiFY9aUYsHXmAVPGpru/6n/CPTMZvdJJt0WffBj00mM5N5M/PevJmNHggEtFiQBC1GpJNxoeu6+mwwmMTfVNDiYUe+BhXgnt0K/s4ocaFML9HoveCjeOwjjWC/E0Xa2q8o0hu89FkBlWtuFUni0AaigKV2FdGN0YCNLMLPCSnPF1AAysEvjxxDgPWOBbtBMtNvgYlObWSX0iszfXY8l6V319kdEbmXW5U89T4r8kO6bnHq/swKd/dZkSSLTv1/FsTYW9nDkLkgD6S4XOVFCFENcsDPMMtMBungNnhuvaBo2jbFa2UqWS9FeK3or9R/UXr2VEo/rJTbbNZ+OyOyGqyKQNwlRuQOeGuRR0QXQ8FpMEp5tpW/+U5HxGuRR0Qo2STdbwfDGFgaaVnB1pGOlkbpuh/oLN33AbW0lQamFcs2bmdq9QDZoAtodjGtxHpRCa6b1C/LSfZ8NmOvWoZMK0EJGA4WgFK7U+tshI29F+sVHXNMebZBeu9nKT2daQ28L3Ri7OepnNsREe+8Cj6BGeCI5MEesffvSmXmgZu8LmGHJ/yzDjow9lTOX6cYslN53z6LDtjCsEUd1Slm+xG/vFYGqJLe8wbMDqNcg1SmnoavdZQiuco7jnKqWsl08FAqIzZ/aW4WxJ5gCehKH29HxLSYCmbx/hXYRCO3EuGl1kv3B7gwuwpRzkXQY40JoYB4fl/K/46xXtD22xkRES6MoLH/DrOMCGW+m4QZ+cEaBlkL9ijvXQG+hncKEb6NJNqcVgWsTyh/Sqo/V8k3CJRJz8U5wXInpyheGPsAqb6DTCszWeCWMUQx0j/Qu2nRoshj1tWseCfDCwk3XGRiQ9V2Dx/s2EhfrrppIWwkgXaRwXhINGw80zSGJpW0t8wgHZbi5Q6xyMJAraSaAeEhMIHhuCprwDiw0I+t7nEwJMSIBOipMqS6R4IrJnmfcOtazFgqy689eynR2eBgMhrUmEyTGja8gr91IcJ4zxSRe91KnnGvIRp2g8efVWEc+LX6rUgoEScj0+LnWnFF4orEFfFcEVWpJp/b0qIcUjhWJNHEjfoprS62Cu0UeaE82+izInLw+M3NgngG7ADdeL+Y4UUhz6ASPZpOYse5Tmt/Ol9uP95o/z/7fC06/p4eaLv9Jl8+5HWwEjmOBsJEEWNkHviswHtueR3NKPmDATWPOL+aw/1EsubNRzU6HY44bbygBftbLYQibRXFv9eKMvkjwAA86NKimRVuCwAAAABJRU5ErkJggg==">';
    
    }
    function showArrow() {
        return '<img width="50" height="50" alt="" src="data:image/jpeg;base64,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">';
    
    }
    function showFolder() {
        return '<img width="50" height="50" alt="" src="data:image/jpeg;base64,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">';
    
    }
    ?>
        </div>
    </body>
</html>