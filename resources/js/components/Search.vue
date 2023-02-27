<template>
    <div class="autocomplete">
        <input
            class="form-control rounded-5"
            placeholder="Search..."
            type="text"
            v-model="keywordLocation"
            @input="handleInputChange"
            @focus="handleFocus"
            @blur="hideDropdown"
        />
        <ul
            v-if="showDropdown"
            class="list-group-item"
            :style="{
                boxShadow:
                    'box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px',
            }"
        >
            <li
                :key="index"
                v-for="(suggestion, index) in suggestions"
                class="list-group-item"
                @click="handleSelect(suggestion.description)"
            >
                {{ suggestion?.description }}
            </li>
        </ul>
    </div>
</template>

<script>
import { debounce } from "lodash";
import { Fetch } from "../utils/fetch/fetch";
export default {
    props: {
        updateKeywordLocation: {
            type: Function,
            required: true,
        },
        keywordLocation: String,
    },
    data() {
        return {
            suggestions: [],
            filteredSuggestions: [],
            showDropdown: false,
            keyword: "",
        };
    },
    methods: {
        async fetchSuggestions(keyword) {
            try {
                const response = await Fetch({
                    method: "GET",
                    path: "/api/restaurant/search",
                    params: { keyword: keyword },
                });
                console.log("===", response);
                if (response.code === 200) {
                    this.suggestions = response.result;
                }
            } catch (error) {
                console.error(error);
            }
        },
        debouncedFetchSuggestions: debounce(function () {
            if (this.keyword.length >= 2) {
                this.fetchSuggestions(this.keyword);
            } else {
                this.filteredSuggestions = [];
                this.showDropdown = false;
            }
        }, 500),
        beforeUnmount() {
            this.debouncedFetchSuggestions.cancel();
        },

        handleFocus() {
            this.showDropdown = true;
        },
        hideDropdown() {
            setTimeout(() => {
                this.showDropdown = false;
            }, 300);
        },
        handleInputChange(event) {
            const inputValue = event.target.value;

            this.keyword = inputValue;
            this.showDropdown = true;
            this.updateKeywordLocation(inputValue);
            // Filter suggestions
            if (this.keyword.length >= 2) {
                this.filteredSuggestions = this.suggestions.filter(
                    (suggestion) =>
                        suggestion.description
                            .toLowerCase()
                            .indexOf(this.keyword.toLowerCase()) !== -1
                );
            } else {
                this.filteredSuggestions = [];
            }
        },
        handleSelect(selectedValue) {
            console.log("selectedValue", selectedValue);
            this.updateKeywordLocation(selectedValue);
            this.filteredSuggestions = [];
            this.keyword = "";
            this.showDropdown = false;
        },
    },
    watch: {
        keywordLocation(newKeywordLocation, oldKeywordLocation) {
            this.filteredSuggestions = this.suggestions.filter((suggestion) =>
                suggestion?.description
                    .toLowerCase()
                    .startsWith(newKeywordLocation.toLowerCase())
            );
        },
        suggestions(newSuggestions, oldSuggestions) {
            this.filteredSuggestions = newSuggestions.filter((suggestion) =>
                suggestion?.description
                    .toLowerCase()
                    .startsWith(this.keywordLocation.toLowerCase())
            );
        },
        keyword(newKeyword, oldKeyword) {
            if (newKeyword) {
                this.debouncedFetchSuggestions();
            } else {
                this.suggestions = [];
            }
        },
        waitTime(newWaitTime, oldWaitTime) {
            this.debouncedFetchSuggestions = debounce(function () {
                this.fetchSuggestions();
            }, newWaitTime);
        },
    },
};
</script>

<style scoped>
.autocomplete {
    width: 100%;
    height: 100%;
    position: relative;
    display: inline-block;
}

.autocomplete input {
    width: 100%;
    height: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: none;
}

.autocomplete ul {
    position: absolute;
    z-index: 1;
    top: 100%;
    left: 0;
    right: 0;
    background-color: #fff;
    border-radius: 5px;
    border: 1px solid #ddd;
    list-style: none;
    margin-top: 0;
    padding-left: 0;
    max-height: 200px;
    overflow-y: scroll;
}

.autocomplete li {
    padding: 10px;
    cursor: pointer;
}

.autocomplete li:hover {
    background-color: #f4f4f4;
}
</style>
