<template>
    <div class="vertical-tab">
        <div class="row no-gutters">
            <div class="col-md-3 pr-md-3 tab-menu">
                <div class="card card-with-shadow border-0">
                    <div class="header-icon">
                        <div class="icon-position d-flex justify-content-center">
                            <div class="tab-icon d-flex justify-content-center align-items-center">
                                <app-icon :name="icon" />
                            </div>
                        </div>
                    </div>
                    <div class="px-primary py-primary">
                        <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                            <template v-for="(tab, index) in filteredTab">
                                <div :key="`tab-${index}-${seletedTab?.name}-${re_render}`">
                                    <div class="text-capitalize tab-item-link d-flex justify-content-between my-2 my-sm-3 d-flex justify-content-between"
                                    style="cursor: pointer;"
                                     v-if="tab.children" @click="tab.expanded = !(!!tab.expanded) ; re_render++">
                                     <span>{{ tab.name }}</span>
                                     <span class="arrow-icon" :class="{ 'arrow-icon--expanded': tab.expanded }"><app-icon name="chevron-right" /></span>
                                    </div>
                                    <a v-else
                                        class="text-capitalize tab-item-link d-flex justify-content-between my-2 my-sm-3"
                                        :class="{ 'active': seletedTab?.name === tab.name }" :id="'v-pills-' + tab.name + '-tab'"
                                        data-toggle="pill" :href="'#' + tab.name + '-' + index"
                                        @click.prevent="tab.children ? loadComponent(tab.children[0]) : loadComponent(tab)">
                                        <span>{{ tab.name }}</span>
                                        <span class="active-icon"><app-icon name="chevron-right" /></span>
                                    </a>
                                    <div v-if="tab.children && tab.expanded">
                                        <a v-for="(child, childrenIndex) in tab.children"
                                            :key="`children-${tab.name}-${childrenIndex}`" href="#"
                                            @click.prevent="loadComponent(child)"
                                            :class="{ 'active': (seletedTab?.name === child.name) }"
                                            class="text-capitalize tab-item-link d-flex justify-content-between my-2 my-sm-3 ml-3">
                                            <span>{{ child.name }}</span>
                                            <span class="active-icon"><app-icon name="chevron-right" /></span>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 pl-md-3 pt-md-0 pt-sm-4 pt-4">
                <div class="card card-with-shadow border-0">
                    <div class="tab-content px-primary">
                        <div class="tab-pane fade active show" :id="componentId">
                            <div class="d-flex justify-content-between">
                                <h5 class="d-flex align-items-center text-capitalize mb-0 title tab-content-header">
                                    {{ componentTitle }}</h5>
                                <div class="d-flex align-items-center mb-0">
                                    <button v-if="!isUndefined(componentButton.label)"
                                        :class="componentButton.class ? componentButton.class : 'btn btn-primary'"
                                        @click.prevent="headerBtnClicked">
                                        {{ componentButton.label }}
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <div class="content py-primary">
                                <component :is="componentName" :props="componentProps" :id="componentId"></component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { TabMixin } from './mixins/TabMixin';

export default {
    name: "VerticalTab",
    mixins: [TabMixin],
    props: {
        icon: {
            type: String,
            require: true
        }
    },
    data(){
        return { re_render: 0 }
    },
    methods: {
        headerBtnClicked() {
            this.$hub.$emit('headerButtonClicked-' + this.componentId, this.tabs[this.currentIndex])
        }
    }
}
</script>