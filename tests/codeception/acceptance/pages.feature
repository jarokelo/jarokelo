Feature: Pages
  In order to valide site
  As a user
  I need to see various text on the page

  Scenario: visit the home page
    Given opened "home" page in the browser
    Then "Járókelő.hu. Ha megosztod, megoldod. - Járókelő.hu" should be visible in the title

  Scenario: visit the new report page
    Given opened "new report" page in the browser
    Then "Probléma bejelentése" should be visible in the title

  Scenario: visit the sign in page
    Given opened "sign in" page in the browser
    Then "Bejelentkezés" should be visible in the title

  Scenario: visit the sign up page
    Given opened "sign up" page in the browser
    Then "Regisztráció" should be visible in the title

  Scenario: visit the about us page
    Given opened "about us" page in the browser
    Then "Hogyan működik" should be visible in the title

  Scenario: visit the reports list page
    Given opened "reports list" page in the browser
    Then "Bejelentések" should be visible in the title

  Scenario: visit the highlighted reports list page
    Given opened "highlighted reports list" page in the browser
    Then "Bejelentések" should be visible in the title

  Scenario: visit the new reports list page
    Given opened "new reports list" page in the browser
    Then "Bejelentések" should be visible in the title

  Scenario: visit the donate page
    Given opened "donate" page in the browser
    Then "Támogasd" should be visible in the title

  Scenario: visit the join us page
    Given opened "join us" page in the browser
    Then "Jelentkezz önkéntesnek" should be visible in the title

  Scenario: visit the institute information page
    Given opened "institute information" page in the browser
    Then "Levelet kapott tőlünk? Így válaszoljon!" should be visible in the title

  Scenario: visit the rss feed page
    Given opened "rss feed" page in the browser
    Then "RSS" should be visible in the title

  Scenario: visit the Budapest city statistics page
    Given opened "statistics budapest city" page in the browser
    Then "Települések statisztikája" should be visible in the title

  Scenario: visit the institutes statistics page
    Given opened "statistics institutes" page in the browser
    Then "Illetékesek statisztikája" should be visible in the title

  Scenario: visit the users statistics page
    Given opened "statistics users" page in the browser
    Then "Felhasználók statisztikája" should be visible in the title

  Scenario: visit the about the team page
    Given opened "team" page in the browser
    Then "A csapat" should be visible in the title

  Scenario: visit the widget page
    Given opened "widget" page in the browser
    Then "Járókelő widget" should be visible in the title

  Scenario: visit the association page
    Given opened "association" page in the browser
    Then "Beszámolók és közhasznúsági jelentések" should be visible in the title

  Scenario: visit the map search page
    Given opened "map search" page in the browser
    Then "Járókelő.hu. Ha megosztod, megoldod." should be visible in the title

  Scenario: visit the terms and agreements page
    Given opened "terms and agreements" page in the browser
    Then "Adatkezelési tájékoztató" should be visible in the title

  Scenario: visit the partners page
    Given opened "partners" page in the browser
    Then "Partnerek és támogatók" should be visible in the title
