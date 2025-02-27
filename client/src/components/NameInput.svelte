<script lang="ts">
  let { name, value } = $props();
</script>

<input type="text" {name} bind:value required />
