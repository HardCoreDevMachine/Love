<script lang="ts">
  const SERVER_URL = import.meta.env.VITE_SERVER_URL;

  async function submitCompatibilityForm(event: Event) {
    event.preventDefault();

    const data = {
      womanName,
      womanAge,
      manName,
      manAge,
    };

    const res = await await fetch(`${SERVER_URL}/api/compatibility/check`, {
      method: "POST",
      body: JSON.stringify(data),
    });

    toggleLoader();

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
    const res = await await fetch(
      "http://localhost:8080/api/compatibility/check/",
      {
        method: "POST",
      }
    );

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

  //TODO: Додель прелоадер
  function toggleLoader() {
    const loader = document.getElementById("loader");
    loader?.classList.toggle("hidden");
  }

  let womanName = $state("Илон Маск");
  let manName = $state("Илона Маскова Цукерберговна");
  let womanAge = $state(18);
  let manAge = $state(21);
  //TODO: Вынести инпуты в отдельные компоненты и реализовать для них валидацию отдельно для каждого типа
</script>

<form id="game-form" method="post" onsubmit={submitCompatibilityForm}>
  <p>ФИО</p>
  <label>
    Жены:
    <!-- Надо закешировать -->
    <input
      type="text"
      name="womanName"
      id="womanName"
      bind:value={womanName}
      required
    />
  </label>
  <label>
    Мужа:
    <!-- Надо закешировать -->
    <input
      type="text"
      name="manName"
      id="manName"
      bind:value={manName}
      required
    />
  </label>
  <p>Возраст</p>
  <label
    >Жены:
    <input
      type="number"
      name="womanAge"
      bind:value={womanAge}
      min="18"
      required
    />
  </label>
  <label>
    Мужа:
    <input type="number" name="manAge" bind:value={manAge} min="18" required />
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
