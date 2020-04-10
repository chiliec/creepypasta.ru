<template>
    <div>
        <div class="editor-container">
            <input class="title" v-model="title"
                   placeholder="Заголовок"
                   maxlength="120" />
            <editor class="editor" :init-data="content"
                    autofocus
                    ref="editor"
                    @save="onSave" />
        </div>
        <div>
            <div @click="save" class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</template>


<script>
    const notifier = require('codex-notifier');

    export default {
        props: ['post'],

        data() {
            return {
                title: '',
                content: {}
            }
        },

        watch: {
            post: {
                immediate: true,
                handler(current) {
                    if (current) {
                        this.title = current.title;
                        this.content = current.content
                    }
                }
            }
        },

        methods: {
            save(data) {
                this.$refs.editor.save()
            },

            request(content) {
                const data = {
                    title: this.title,
                    content: content,
                };
                if (this.post) {
                    return axios.patch(`/posts/${this.post.id}`, data);
                } else {
                    return axios.post('/posts', data);
                }
            },

            onSave(content) {
                this.request(content).then((result) => {
                    let url = result.data.url;
                    if (url) {
                        window.location.href = url;
                    } else {
                        window.location.href = '/';
                    }
                }).catch(({response: {data: {errors}}}) => {
                    let key = _.head(_.keys(errors));

                    if (key) {
                        let message = _.get(errors, `${key}.0`);
                        if (message) {
                            console.log(message);
                            notifier.show({message: message, style: 'error'});
                        }
                    }
                })
            }
        }
    }
</script>

<style module>
    .editor-container {
        background-color: #fff;
        max-width: 660px;
        margin-left: auto;
        margin-right: auto;
    }
    .editor-container .title {
        resize: none;
        border: 0;
        height: 46px;
        font-size: 36px;
        font-weight: 500;
        width: 100%;
    }
</style>