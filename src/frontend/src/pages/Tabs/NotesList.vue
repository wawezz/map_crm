<template>
  <div>
    <div class="row">
      <div class="col-12">
        <ul class="notes">
          <li
            class="note"
            v-for="note in notes"
            :key="note._id.$oid"
            v-if="note.noteType==$store.state.noteTypes.NOTE_TYPE_COMMON.id || note.noteType==$store.state.noteTypes.NOTE_TYPE_TASK_CREATED.id || note.noteType==$store.state.noteTypes.NOTE_TYPE_TASK_RESULT.id"
          >
            <b v-if="note.dataValue.workerId">{{'worker ID: ' + note.dataValue.workerId}}</b>
            <b v-if="note.dataValue.text">{{note.dataValue.text}}</b>
            <div
              class="taskBlock"
              v-if="note.noteType==$store.state.noteTypes.NOTE_TYPE_TASK_RESULT.id"
            >
              <div class="taskBlock__item">
                Task
                <b>N {{note.dataValue.data.task.id}}</b>
                result: {{note.dataValue.data.result}}
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <base-input>
          <label>Add note comment</label>
          <textarea rows="4" class="form-control" v-model="noteComment.data"></textarea>
          <button
            class="btn btn-primary mt-2"
            @click.prevent="saveNoteComment(elementType, element)"
          >Save</button>
        </base-input>
      </div>
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
    element: {
      type: Object,
      default: () => {
        return {};
      }
    },
    noteComment: {
      type: Object,
      default: () => {
        return {};
      }
    },
    elementType: { type: Number },
    saveNoteComment: { type: Function }
  }
};
</script>
