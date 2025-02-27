<script lang="ts">
  let {name,value} = $props();
</script>

<input type="number" name={name} bind:value required />
