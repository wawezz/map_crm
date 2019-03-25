<template>
  <div class="row">
    <div class="col-12">
      <ul class="notes">
        <li
          class="note"
          v-for="note in notes"
          :key="note._id.$oid"
          v-if="note.noteType!=$store.state.noteTypes.NOTE_TYPE_COMMON.id"
        >
          <b>{{new Date(note.createdAt*1000) | dateTime}} {{noteTypes[note.noteType]}}</b>,
          created by:
          <router-link
            :to="'/profile/' + note.createdBy"
          >{{managersById[note.createdBy]?managersById[note.createdBy].name:note.dataValue.by}}</router-link>
          <b v-if="note.dataValue.text">{{note.dataValue.text}}</b>
          <span v-if="note.noteType == noteType">
            <b>{{note.dataValue.data.field}}:</b>
            {{note.dataValue.data.values.from?note.dataValue.data.values.from:'null'}} -> {{note.dataValue.data.values.to?note.dataValue.data.values.to:'null'}}
          </span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    notes: {
      type: Array,
      default: () => {
        return [];
      }
    },
    managersById: {
      type: Object,
      default: () => {
        return {};
      }
    },
    noteTypes: {
      type: Array,
      default: () => {
        return {};
      }
    },
    noteType: { type: Number }
  }
};
</script>
