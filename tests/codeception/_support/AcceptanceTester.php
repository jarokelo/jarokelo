<?php

const PAGE_MAP = [
    "home" => "/",
    "new report" => "/uj-bejelentes",
    "sign in" => "/bejelentkezes",
    "sign up" => "/regisztracio",
    "reports list" => "/bejelentesek",
    "highlighted reports list" => "/bejelentesek/kiemelt",
    "new reports list" => "/bejelentesek/friss",
    "about us" => "/rolunk/hogyan-mukodik",
    "donate" => "/rolunk/tamogasd",
    "join us" => "/rolunk/csatlakozz",
    "institute information" => "/rolunk/hivatal",
    "team" => "/rolunk/csapat",
    "rss feed" => "/rss/index",
    "statistics budapest city" => "/statisztikak/varosok/budapest",
    "statistics institutes" => "/statisztikak/illetekesek",
    "statistics users" => "/statisztikak/felhasznalok",
    "widget" => "/widget/configure",
    "association" => "/rolunk/egyesulet",
    "map search" => "/terkep",
    "terms and agreements" => "/rolunk/felhasznalasi-feltetelek",
    "partners" => "/rolunk/partnerek"
];

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

   /**
    * @Given opened :page_name page in the browser
    */
    public function openedHomePageInTheBrowser($page_name)
    {
        $this->amOnPage(PAGE_MAP[$page_name]);
    }

   /**
    * @Then :title should be visible in the title
    */
    public function jrkelShouldBeVisibleInTheTitle($title)
    {
        $this->seeInTitle($title);
    }
}
