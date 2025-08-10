<script lang="ts">
      document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("couple-form");
        const submitBtn = document.getElementById("submit-btn");
        const cacheSubmitBtn = document.getElementById("cache-submit-btn");
        const resultContainer = document.getElementById("result-container");

        function submitForm(useCache = false) {

          const formData = {
            wifeName: form?.querySelector('[name="wife-name"]')?.value,
            wifeAge: Number(form?.querySelector('[name="wife-age"]')?.value),
            husbandName: form?.querySelector('[name="husband-name"]')?.value,
            husbandAge: Number(
              form.querySelector('[name="husband-age"]').value
            ),
            useCache: useCache,
          };

          fetch("/api/game-form", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
          })
            .then((response) => response.json())
            .then((data) => {
              // Скрываем форму
              form.style.display = "none";
              message = data["success"] ? "Женаты" : "Не пара";

              // Показываем результат (можно адаптировать под ваш формат ответа)
              resultContainer.style.display = "block";
              resultContainer.innerHTML = `<p>Результат: ${message ?? 'Не пара'}</p>`;

              // Или можно обработать данные более красиво, в зависимости от того, что возвращает сервер
            })
            .catch((error) => {
              console.error("Error:", error);
              alert("Произошла ошибка при отправке данных");
            });
        }

        submitBtn.addEventListener("click", () => submitForm(false));
        cacheSubmitBtn.addEventListener("click", () => submitForm(true));
      });
    </script>

    <h1>ЗАДАНИЕ</h1>
    <p>
      создать веб форму реализовать игру удаленная симпатия которая позволяет
      определить совместимость людей по их имени и по возрасту, если разница в
      возрасте менее 10 лет и разница суммы букв в фио влюбленных менее 10,то
      они подходят друг другу если условия не выполняются то не подходят друг
      другу
    </p>
    <p>
      в этой же странице реализовать многократный перебор спутников реализовать
      кнопку очистки форм ввода возраста и фио и по новой ввести добавить фото
      результата при удачной симпатии картинка с видом на загс если симпатии нет
      то картинка произвольного вида
    </p>
    <form id="couple-form">
      <p>Жена</p>
      <input type="text" name="wife-name" placeholder="Имя жены" required />
      <input
        type="number"
        name="wife-age"
        placeholder="Возраст жены"
        required
      />

      <p>Муж</p>
      <input type="text" name="husband-name" placeholder="Имя мужа" required />
      <input
        type="number"
        name="husband-age"
        placeholder="Возраст мужа"
        required
      />

      <button type="button" id="submit-btn" value="form-calc">Посчитать</button>
      <button type="button" id="cache-submit-btn">Посчитать в кеше</button>
      <input type="reset" value="Очистить" />
    </form>

    <div id="result-container" style="display: none"></div>
    <style>
      :root {
        --main-color: #fe7f2d;
        --background-color: #233d4d;
      }

      * {
        color: var(--main-color);
        background-color: var(--background-color);
        font-family: Verdana, Geneva, sans-serif;
      }

      input {
        margin-top: 0.5rem;
        outline-width: 0;
        border-color: var(--main-color);
      }

      input::placeholder {
        font-weight: bold;
        opacity: 0.5;
        color: var(--main-color);
      }
    </style>