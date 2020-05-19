<template>
  <span>

    <span v-if="showBadge">
      <b-badge id="popover-reactive-1" class="bg-p1-pink ml-2" style="font-size: 16px">2</b-badge>
      <b-popover target="popover-reactive-1" class="w-100"
        triggers="hover"
        placement="auto">
          <span class="font-weight-bold">Periodiniai mokėjimai:</span>
          <br class="mb-2">
          2020-05-19 Mokesčiai 40
          <br>
          <b-button class="btn btn-sm bg-main-teal">Pridėti</b-button>
          <b-button class="btn btn-sm bg-p1-dark">Peržiūrėti</b-button>
          <hr>
          2020-05-19 Maistas 3.5
          <br>
          <b-button class="btn btn-sm bg-main-teal">Pridėti</b-button>
          <b-button class="btn btn-sm bg-p1-dark">Peržiūrėti</b-button>
      </b-popover>
    </span>
  </span>
</template>

<script>
export default {
  date() {
    return {
      showBadge: false,
    }
  },
}
</script>