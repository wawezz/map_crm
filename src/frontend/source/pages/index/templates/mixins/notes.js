import axios from "axios";
import AuthService from "~/pages/index/services/AuthService";

export const notes = {
    data() {
        return {
            notesFilter: '[]',
            notesSort: '[]',
            notes: [],
            noteComment: null,
            noteInProgress: false,
            noteError: {
                timeoutID: null,
                message: null
            },
            noteResponse: {
                timeoutID: null,
                active: false
            },
            notesLimit: 20
        }
    },
    computed: {
        notesPage() {
            let page = 1;
            if (this.$route.params.page) page = this.$route.params.page;

            return page;
        },
        notesPages() {
            let pages = 0;
            if (this.notes.count) pages = Math.ceil(this.notes.count / this.notesLimit);

            return pages;
        }
    },
    methods: {
        getNotes() {
            const filter = this.notesFilter !== '[]' ? JSON.stringify(this.notesFilter) : this.notesFilter;
            // const sort = this.notesSort !== '[]' ? JSON.stringify(this.notesSort) : this.notesSort;
            axios({
                method: "post",
                url: "/v1/note/list?filter=" + filter + "&limit=" + this.notesLimit + "&skip=" + (this.notesLimit * (this.notesPage - 1)),
                data: {
                    params: { id: AuthService.uid }
                },
                headers: {
                    Authorization: `Bearer ${AuthService.tokens.access}`
                }
            })
                .then(response => {
                    this.notes = response.data;
                    console.log(this.notes);
                })
                .catch(e => {
                    const data = e.response.data;

                    if (data.error) {
                        if (data.error.code === 418 && data.error) {
                            this.listError = data.error.message;
                        } else if (data.error.code === 403 && data.error) {
                            this.listError = "You are not allowed to access this content.";
                        }
                    } else {
                        console.error("Unexpected error", data.error);
                    }
                });
        },
        saveNoteComment(type, element) {
            if (this.noteInProgress === true || !this.noteComment) return;
            let formData;
            this.noteError = {};
            const vm = this;
            this.noteInProgress = true;
            /* eslint-disable */
            formData = new FormData();
            /* eslint-enable */
            formData.append("params[id]", AuthService.uid);
            formData.append("form[elementId]", element.id);
            formData.append("form[elementType]", type);
            formData.append("form[dataValue]", this.noteComment);

            axios({
                method: "post",
                url: "/v1/note/comment",
                data: formData,
                headers: {
                    Authorization: `Bearer ${AuthService.tokens.access}`
                }
            }).then(
                ({ data }) => {
                    vm.noteInProgress = false;
                    vm.notes.push(data);
                    vm.noteComment = null;
                    clearTimeout(vm.noteResponse.timeoutID);
                    vm.noteResponse.active = true;
                    vm.noteResponse.timeoutID = setTimeout(function () {
                        vm.noteResponse.active = false;
                    }, 3000);
                },
                ({ response }) => {
                    vm.noteInProgress = false;

                    const { data } = response;

                    if (data.error) {
                        if (data.error.code === 418 && data.error) {
                            clearTimeout(vm.noteError.timeoutID);
                            if (data.noteError.errors) {
                                vm.noteError.message = JSON.stringify(data.error.errors);
                            } else {
                                vm.noteError.message = data.error.message;
                            }
                            vm.noteError.timeoutID = setTimeout(function () {
                                vm.noteError.message = null;
                            }, 3000);
                        }
                    } else {
                        console.error("Unexpected error", data.error);
                    }
                }
            );
        }
    }
}
