<?php
// 🔌 1. Połączenie z bazą danych MySQL
$polaczenie = mysqli_connect("localhost", "root", "", "newsletter");

// 💥 Sprawdź, czy połączenie się udało
if (!$polaczenie) {
    die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
}

// 📨 2. Pobieranie danych z formularza metodą POST
$imie = $_POST['imie'];
$nazwisko = $_POST['nazwisko'];
$email = $_POST['email'];
$plec = $_POST['plec'] ?? "WoleNiePodawac"; // jeśli nic nie zaznaczono

// ✅ Zgody – jeśli checkbox nie jest zaznaczony, nie pojawi się w POST
$zgoda1 = isset($_POST['zgoda1']) ? 1 : 0;
$zgoda2 = isset($_POST['zgoda2']) ? 1 : 0;
$zgoda3 = isset($_POST['zgoda3']) ? 1 : 0;

// 🛡️ WAŻNE: Zabezpieczenie danych przed atakami SQL injection
$imie = mysqli_real_escape_string($polaczenie, $imie);
$nazwisko = mysqli_real_escape_string($polaczenie, $nazwisko);
$email = mysqli_real_escape_string($polaczenie, $email);
$plec = mysqli_real_escape_string($polaczenie, $plec);

// 🧠 3. Wstawienie użytkownika do tabeli `uzytkownicy`
$zapytanie_uzytkownik = "
    INSERT INTO uzytkownicy (imie, nazwisko, email, plec)
    VALUES ('$imie', '$nazwisko', '$email', '$plec')
";

if (mysqli_query($polaczenie, $zapytanie_uzytkownik)) {
    // 🆔 Pobierz ID nowego użytkownika, by powiązać go z tabelą `zgody`
    $id_uzytkownika = mysqli_insert_id($polaczenie);

    // 📩 4. Wstawienie zgód do tabeli `zgody`
    $zapytanie_zgody = "
        INSERT INTO zgody (uzytkownik_id, zgoda1, zgoda2, zgoda3)
        VALUES ($id_uzytkownika, $zgoda1, $zgoda2, $zgoda3)
    ";

    if (mysqli_query($polaczenie, $zapytanie_zgody)) {
        echo "<p>Dziękujemy za zapis do newslettera!</p>";
    } else {
        echo "<p>Błąd podczas zapisu zgód: " . mysqli_error($polaczenie) . "</p>";
    }

} else {
    echo "<p>Błąd przy zapisie użytkownika: " . mysqli_error($polaczenie) . "</p>";
}

// 🔚 5. Zamknięcie połączenia z bazą danych
mysqli_close($polaczenie);
?>
