import coreLibrary from "../../../helpers/CoreLibrary";

export const TabMixin = {
    extends: coreLibrary,
    props: {
        tabs: {
            type: Array,
            require: true
        }
    },
    data() {
        return {
            queryStringKey: 'tab',
            queryStringValue: '',
            currentIndex: 0,
            componentName: '',
            componentId: '',
            componentProps: '',
            componentTitle: '',
            componentButton: {},
            seletedTab: null
        }
    },
    computed: {
        filteredTab() {
            return this.tabs.filter(tab => (this.isUndefined(tab.permission) || tab.permission !== false))
        }
    },
    mounted() {
        this.queryStringValue = this.getQueryStringValue(this.queryStringKey);
        this.setTabByName();
    },
    methods: {
        setQueryString(name) {
            const pageTitle = window.document.title;
            window.history.pushState("", pageTitle, `?${this.queryStringKey}=${name}`);
        },

        setTabByName() {
            let currentTab = currentTab = this.filteredTab[0].children ? this.filteredTab[0].children[0] : this.filteredTab[0];                

            for (let item of this.filteredTab) {
                if (item.children) {
                    for (let child of item.children) {
                        if (child.name == this.queryStringValue) {
                            currentTab = child;
                            item.expanded = true;
                            break;
                        }
                    }
                } else {
                    if (item.name == this.queryStringValue) {
                        currentTab = item;
                        break;
                    }
                }
            }
            for (let item of this.filteredTab) {
                if (item.children) {
                    for (let child of item.children) {
                        if (child.name == currentTab.name) {
                            item.expanded = true;
                            break;
                        }
                    }
                }
            }            
            this.seletedTab = currentTab
            this.loadComponent(currentTab, this.currentIndex);
        },
        loadComponent(tab) {
            if (!tab.children) {
                this.seletedTab = tab
                this.componentTitle = tab.title;
                this.componentId = tab.name + '-';
                this.componentName = tab.component;
                this.componentProps = tab.props;
                if (!this.isUndefined(tab.headerButton)) {
                    this.componentButton = tab.headerButton;
                } else this.componentButton = {};
                this.setQueryString(tab.name);
            }
        }
    }
};
