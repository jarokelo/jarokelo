<?php
    $asset = \app\assets\AppAsset::register($this);
?>

<aside class="hero hero--bureau hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="container">
            <div class="row center-xs">
                <div class="col-xs-12 col-md-8">
                    <h2 class="heading heading--1 hero__title"><?= Yii::t('bureau', 'contact.title'); ?></h2>
                    <p class="hero__lead hero__lead--padding">
                        <?= Yii::t('bureau', 'contact.content'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>
<div class="container bureau">
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <h2 class="heading heading--6">Információk önkormányzatoknak, szolgáltatóknak</h2>

            <p>
                A myProjectn közterületi problémákat jelenthetnek be az állampolgárok. A problémák leírásához minden esetben kérjük a bejelentőktől, hogy adják meg az érintett utca nevét és a települést, vagy kerületet. A bejelentéshez a beküldők fotót is csatolnak a problémáról, gyakran többet is, sőt, az is előfordul, hogy a csatolt kép egy átrajzolt térkép, amely segít a probléma kivizsgálásában.
            </p>

            <p>
                A tőlünk kapott levélben megtalálja a probléma tárgyát, helyszínét, a bejelentő által írt leírást, és egy linket a fotókhoz. Minden esetben érdemes megnyitnia a linket és ellenőriznie, hogy a bejelentés beküldése óta történt-e valamilyen fejlemény az ügyben. A csatolt fotók segítenek a probléma kivizsgálásában is.
            </p>

            <h2 class="heading heading--6">Hogyan válaszoljon?</h2>
            <p>
              A myProjectről érkező bejelentéseket MYPROJECT-VÁROSNEVE-AZONOSÍTÓSZÁM modellben képzett egyedi azonosítókkal látjuk el. A myProjectt kiszolgáló szoftver és önkénteseink akkor tudják azonosítani, melyik bejelentésre válaszol, ha az e-mail tárgyába, vagy szövegébe bemásolja ezt a számot.
                Ezek a feltöltés után azonnal nyilvánosan elérhetők lesznek az interneten a myProject oldalon keresztül. A válasz feltöltéséről oldaunk automatikusan értesíti a bejelentőt is.

                <div class="image-holder">
                    <img class="" src="<?= $asset->baseUrl; ?>/images/example.png" alt="">
                </div>
                Postafiókunk kezelői naponta egyszer nézik át a levelezést, és teszik közzé a válaszleveleket a
                myProjectn.
            </p>

            <h2 class="heading heading--6">Mit írjon a válaszába?</h2>
            <p> A válaszlevélben érdemes leírnia, milyen vizsgálatot végzett, vagy intézkedést tett a szervezete a probléma
                megoldása érdekében. Amennyiben a megoldás nem azonnal történik, érdemes tervezett időpontot megadnia.
            </p>
            <p>A pozitív válaszlevél kulcsa minden esetben a pozitív hangvétel és a megoldásra törekvés kifejezése. Az igazán
                nyílt szemléletű önkormányzatok városgazdái fényképet is csatolnak a probléma megoldásáról.
            </p>
            <p><em><a class="link--info" href="http://myproject.hu/budapest/bejelentesek/4153/szep-katyu-az-ut-kozepen-50100-cm">Példa pozitív
                        válaszlevélre.</a></em></p>

            <h2 class="heading heading--6">Miért válaszoljon?</h2>
            <p>A myProjectre érkező bejelentések a közterületi problémák gyorsabb megoldását segítik elő, és emellett segítenek
                a hivatalos illetékesnek és a városlakóknak, vagy a szolgáltatások használóinak abban, hogy közösen megvitassák,
                mi a megfelelő megoldás egy problémára.
            </p>
            <p>Ha válaszol, pozitív képet alakít ki munkájáról és segít abban, hogy az állampolgárok megbízzanak az
                önkormányzatokban, közpénzből működtetett szervezetekben, vagy éppen elégedettek legyenek az Ön
                szolgáltatásával.
            </p>
            <p>Engedje meg, hogy felhívjuk szíves figyelmét arra, hogy a myProject-ből érkező e-mailek a panaszokról és a közérdekű
                bejelentésekről szóló 2013. évi CLXV. törvény (a továbbiakban: Törvény) 1. § (3) bekezdése szerinti közérdekű
                bejelentésnek minősül, amelyre a Törvény 2. § (1) bekezdése szerinti ügyintézési határidő irányadó.
            </p>
            <p><em> <a class="link--info" href="http://myproject.hu/budapest/bejelentesek/5448/katyuk-a-bringauton">Példa az eredményes
                        kapcsolattartásra, amely segít a bizalomnövelésben.</a></em></p>

            <h2 class="heading heading--6">Mi alapján kapják a problémák a státusz címkéket?</h2>
            <p>A bejelentés publikálása után azonnal a „Válaszra vár” címkét kapja. Ekkor még nem érkezett semmilyen válasz az
                illetékes szervezettől, vagy ha több érintett van, akkor a további érintettek válaszát várjuk.
            </p>
            <p>A myProject oldal ügykezelői „Megoldott”-nak azt a bejelentést minősítik, amelynél a bejelentő, más felhasználók,
                vagy az illetékes értesítést küldött arról, hogy a probléma megoldása lezárult, azaz kijavították a hibát, vagy
                elhárították a problémát, vagy esetleg minden fél számára megfelelő kompromisszum jött létre.
            </p>
            <p>Az olyan bejelentéseknél, amelyekre érkezik hivatalos válasz, de a megoldás időpontja nem bizonyos, a myProject
                az „Megoldásra vár” címkét használja, amely azt jelenti, hogy a bejelentőtől, vagy az illetékestől várunk további
                információt arról, hogy tényleg megoldódott-e a probléma. Az ilyen bejelentéseket havonta egyszer megpróbáljuk
                végeleges, „Megoldott” vagy „Megoldatlan” státuszba sorolni a levelezés alapján.
            </p>
            <p>Az olyan bejelentéseknél, amelyeknél egyértelműen nem érkezett reakció az illetékestől, vagy az illetékes
                elutasította a probléma elhárítását, nem jött létre az érintett felek közt kompromisszum, vagy esetleg
                bizonytalan határidejű kivitelezést helyezett kilátásba, a myProject oldalon a „Megoldatlan” címkét használjuk. A
                bejelentéseket havonta egyszer átnézzük és igyekszünk őket a levelezés alapján besorolni, illetve újraértesíteni
                az illetékeseket.
            </p>

            <h2 class="heading heading--6">Technikai megközelítés: a válasz formátuma</h2>
            <p>Ideális esetben a válaszlevél formátuma szöveg, amelyet közvetlenül e-mailben küld el nekünk. Ha dokumentumot
                csatol, akkor is minden esetben javasoljuk, hogy szövegfájlt, vagy szöveg réteggel rendelkező pdf fájlt osszon
                meg. Ha számítógéppel feldolgozható formátumokat használ, segít abban, hogy minél többen tudják olvasni és
                használni az információkat, amelyeket megoszt.
            </p>
            <h2 class="heading heading--6">Lépjen velünk kapcsolatba!</h2>
            <p>Amennyiben bármilyen információra, vagy segítségre van szüksége azzal kapcsolatban, hogyan használja a
                myProject-t, kérjük, írjon a info@myproject.hu címre. Kapcsolattartó: Kolléga, titulus.
            </p>
            <br>
        </div>
        <div class="col-xs-12 col-md-4">
            <div class="panel panel--info panel--label-offset">
                <div class="panel__body">
                    <p>Nem találja városát, települését?</p>
                    <p>Ha szeretné, hogy felkerüljön a városa vagy települése a myproject oldalra, írjon nekünk a <a class="link--info" href="mailto:info@myproject.hu">info@myproject.hu</a> címre.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('/_snippets/_hero-bottom-dual') ?>
