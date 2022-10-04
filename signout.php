<?
include "includes/config.inc.php";
  mysql_query("UPDATE admin SET logged=0 WHERE id='".$_SESSION['user_id']."'");


  session_destroy();
     unset($_COOKIE['userid']);
      unset($_COOKIE['username']);
      unset($_COOKIE['usertype']);
       unset($_COOKIE['userpermission']);
        unset($_COOKIE['userclient']);
        unset($_COOKIE['page_access']);
         unset($_COOKIE['material_access']);
        unset($_COOKIE['access_limit']);
       unset($_COOKIE['show_part']);
       unset($_COOKIE['landing_page']);
       unset($_COOKIE['link_detail_page']);
       unset($_COOKIE['showgraph']);
       unset($_COOKIE['credit']);
        unset($_COOKIE['showkp']);
         unset($_COOKIE['credituse']);
          unset($_COOKIE['manf_access']);
           unset($_COOKIE['user_company']);
           unset($_COOKIE['d_manf']);
           unset($_COOKIE['d_vds']);
 setcookie('userid', null, -1, '/');
    setcookie('username', null, -1, '/');
    setcookie('usertype', null, -1, '/');
    setcookie('page_access', null, -1, '/');
    setcookie('material_access', null, -1, '/');
        setcookie('access_limit', null, -1, '/');
        setcookie('show_part', null, -1, '/');
        setcookie('landing_page', null, -1, '/');
        setcookie('link_detail_page', null, -1, '/');
        setcookie('showgraph', null, -1, '/');
         setcookie('credit', null, -1, '/');
          setcookie('showkp', null, -1, '/');
           setcookie('credituse', null, -1, '/');
            setcookie('manf_access', null, -1, '/');
            setcookie('user_company', null, -1, '/');
            setcookie('d_manf', null, -1, '/');
            setcookie('d_vds', null, -1, '/');

  header("Location: index.php");
?>
