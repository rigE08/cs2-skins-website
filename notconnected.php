<?php
require_once 'steamauth/steamauth.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styleTemplate.css">
    <title>ZUTV | Skins</title>
</head>

<body>
    <div class="container-lg">


        <div class="container-fluid px-4 py-5 my-5 text-center" style="height: 79vh">
            <div class="lc-block d-block mx-auto mb-4">
                <img class="w-25" src="zutvv2.png" alt="Logo ZUTV">
                </svg>
            </div>
            <div class="lc-block">
                <div editable="rich">

                    <h2 class="display-5 fw-bold">Alege-ți skin-ul și transformă-ți experiența!</h2>

                </div>
            </div>
            <div class="lc-block col-lg-6 mx-auto mb-4">
                <div editable="rich">

                    <p class="lead ">Pentru a-ți personaliza experiența și a alege skinurile dorite, trebuie să te
                        conectezi cu contul tău Steam. Procesul este simplu și securizat, iar informațiile salvate sunt
                        strict limitate la:
                        <hr>
                    <ol>
                        <li><b>1. Steamid: </b>Pentru a asigura autentificarea ta în mod sigur și a oferi acces la
                            funcționalitățile personalizate.</li>
                        <li>
                            <b>2. Datele despre skinurile selectate: </b>Acest lucru include preferințele tale în ceea ce privește
                            skinurile.
                        </li>
                    </ol>

                </div>
            </div>

            <div class="lc-block d-grid gap-2 d-sm-flex justify-content-sm-center">
                <?= loginbutton("square") ?>
            </div>
        </div>




    </div>
</body>
<script>
sessionStorage.setItem("selectedCategory", "all");
</script>

</html>