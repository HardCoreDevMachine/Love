<script lang="ts">
// Надо переосмыслить т.к. тут идёт строго расчёт, а название намекает что она будет чистой
function calcMatch(): void {

}
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

  <button type="button" id="submit-btn" onclick={calcMatch} value="form-calc">Посчитать</button>
  <button type="button" id="cache-submit-btn">Посчитать среди имеющихся</button>
  <input type="reset" value="Очистить" />
</form>

<div id="result-container" style="display: none"></div>