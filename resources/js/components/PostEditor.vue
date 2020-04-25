<template>
    <div>
        <div class="editor-container">
            <input class="title"
                   v-model="title"
                   placeholder="Заголовок"
                   maxlength="120" />
            <div id="editor-js" class="editor-js"></div>
        </div>
        <div class="buttons-container">
            <div @click="save" class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</template>


<script>
    const notifier = require('codex-notifier');

    export default {
        props: ['post'],

        created() {
            let self = this;
            let tools = {};
            let data = self.content;

            const Paragraph = require('@editorjs/paragraph');
            tools.paragraph = {
                class: Paragraph,
                inlineToolbar: true
            };

            const Header = require('@editorjs/header');
            tools.header = {
                class: Header,
                config: {
                    placeholder: "Heading"
                }
            };

            const LinkTool = require('@editorjs/link');
            tools.linkTool = {
                class: LinkTool,
                config: {
                    endpoint: '/fetch-link',
                }
            };

            const EditorJS = require('@editorjs/editorjs');
            let editor = new EditorJS({
                holderId: 'editor-js',
                tools,
                initialBlock: 'paragraph',
                placeholder: 'Enter text',
                autofocus: true,
                data: data,
                onReady: function () {},
                onChange: function () {
                    editor.save().then((savedData) => {
                        self.handleChange(savedData);
                    })
                }
            });
        },

        data() {
            return {
                title: '',
                content: {}
            }
        },

        watch: {
            post: {
                immediate: true,
                handler(post) {
                    if (post) {
                        this.title = post.title;
                        this.content = post.content;
                    }
                }
            }
        },

        methods: {
            handleChange(content) {
                this.content = content;
            },
            save() {
                const data = {
                    title: this.title,
                    content: this.content
                };
                let request;
                if (this.post) {
                    request = axios.default.patch(`/posts/${this.post.id}`, data);
                } else {
                    request = axios.default.post('/posts', data);
                }
                request
                    .then((result) => {
                        let url = result.data.url;
                        if (url) {
                            window.location.href = url;
                        } else {
                            window.location.href = '/';
                        }
                    })
                    .catch(({ response: { data: {errors} }}) => {
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