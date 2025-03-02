<script lang="ts">
  import AgeInput from "./AgeInput.svelte";
  import NameInput from "./NameInput.svelte";

  const SERVER_URL = import.meta.env.VITE_SERVER_URL;

  async function submitCompatibilityForm(event: Event) {
    event.preventDefault();

    const data = {
      woman: {
        name: womanName,
        age: womanAge,
      },
      man: {
        name: manName,
        age: manAge,
      },
    };

    const res = await await fetch(`${SERVER_URL}/api/compatibility/check`, {
      method: "POST",
      body: JSON.stringify(data),
    });

    const json = await res.json();

    toggleLoader();
    if (res.status === 200) {
      onSuccess(json);
    } else {
      onError("Some error occured...");
    }
  }

  async function getPossibleMatches(event: Event) {
    event.preventDefault();
    if (event.target === null || !(event.target instanceof HTMLFormElement)) {
      return;
    }

    toggleLoader();
    const res = await await fetch(`${SERVER_URL}/api/compatibility/check/`, {
      method: "POST",
    });

    toggleLoader();
    if (res.status === 200 && event.target instanceof HTMLFormElement) {
      //TODO: Разработать страницу onSuccess для нескольких пар
      // Убрать заглушку
      onSuccess(true);
    } else {
      onError("Some error occured...");
    }
  }

  //TODO: Сделать валидацию
  function onError(error: string) {
    alert(error);
  }

  //TODO: Сделать редирект
  //TODO: Разработать страницу onSuccess для одной пары
  function onSuccess(compatibility: boolean) {
    alert("Ваши пара" + (compatibility && "не") + "совпала");
  }

  //TODO: Доделать прелоадер
  function toggleLoader() {
    const loader = document.getElementById("loader");
    loader?.classList.toggle("hidden");
  }

  let womanName = $state("Илон Маск");
  let manName = $state("Илона Маскова Цукерберговна");
  let womanAge = $state(18);
  let manAge = $state(21);
</script>

<form id="game-form" onsubmit={submitCompatibilityForm}>
  <p>ФИО</p>
  <label>
    Жены:
    <NameInput value={womanName} />
  </label>
  <label>
    Мужа:
    <NameInput value={manName} />
  </label>
  <p>Возраст</p>
  <label
    >Жены:
    <AgeInput value={womanAge} />
  </label>
  <label>
    Мужа:
    <AgeInput value={manAge} />
  </label>
  <br />

  <button name="isCompared">Расчитать совместимость</button>
  <button type="button" name="getMatches" onclick={getPossibleMatches}
    >Найти возможные пары</button
  >
  <button type="reset">Очистить</button>
</form>
<div id="loader" class="hidden">Отправляем...</div>

<style>
  .hidden {
    display: none;
  }
</style>
