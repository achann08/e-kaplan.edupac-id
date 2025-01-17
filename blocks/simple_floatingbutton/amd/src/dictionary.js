define(['jquery'], function($) {
    function cleanText(text) {
        if (typeof text !== 'string') {
            return text;
        }
        text = text.replace(/\{it\}/g, '<i>');
        text = text.replace(/\{\/it\}/g, '</i>');
        text = text.replace(/{d_link\|([^|]+)\|([^}]+)}/g, '$1');
        text = text.replace(/{a_link\|([^}]+)}/g, '$1');
        return text;
    }

    function performSearch(searchword) {
        var originalSearchword = searchword;
        searchword = searchword.toLowerCase().replace(/\s+/g, "-");

        var dictionaryType = $("input[name='dictionaryType']:checked").val();
        var apiKey = dictionaryType === "collegiate" ? "5430a298-0083-42b3-bf07-6052f1f7cb09" : "0efb0a45-3fda-45e2-a255-b820fa948f2e";
        var url = "https://www.dictionaryapi.com/api/v3/references/" + dictionaryType + "/json/" + searchword + "?key=" + apiKey;

        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            beforeSend: function() {
                var spinner = '<div class="d-flex justify-content-center mt-3"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';
                $("#dictionary-results").html(spinner);
            },
            success: function(data) {
                var results = "";
                var hasExactMatches = false;
                var suggestions = [];

                if (Array.isArray(data)) {
                    if (typeof data[0] === 'string') {
                        suggestions = data;
                    } else {
                        var filteredData = data.filter(function(entry) {
                            var entryId = entry.meta && entry.meta.id ? entry.meta.id.split(':')[0].toLowerCase() : "";
                            var stems = entry.meta && entry.meta.stems ? entry.meta.stems.map(stem => stem.toLowerCase()) : [];
                            return entryId === originalSearchword.toLowerCase() || stems.includes(originalSearchword.toLowerCase());
                        });

                        if (filteredData.length > 0) {
                            var uniqueId = new Date().getTime();
                            results += '<div class="accordion mt-3" id="accordion-' + uniqueId + '">';
                            $.each(filteredData, function(index, entry) {
                                var entryId = entry.meta.id.split(':')[0];
                                var fl = entry.fl || 'N/A';
                                var collapseId = 'collapse' + uniqueId + '-' + index;
                                var headingId = 'heading' + uniqueId + '-' + index;

                                results += '<div class="card">';
                                results += '<div class="card-header p-0" id="' + headingId + '">';
                                results += '<button class="btn btn-link text-left font-weight-bold" type="button" data-toggle="collapse" data-target="#' + collapseId + '" aria-expanded="true" aria-controls="' + collapseId + '">';
                                results += entryId + ' (' + fl + ')';
                                results += '</button></div>';
                                results += '<div id="' + collapseId + '" class="collapse" aria-labelledby="' + headingId + '" data-parent="#accordion-' + uniqueId + '">';
                                results += '<div class="card-body overflow-auto" style="max-height: 250px;"><ol class="m-0">';

                                $.each(entry.def, function(defIndex, defEntry) {
                                    $.each(defEntry.sseq, function(sseqIndex, sseqEntry) {
                                        $.each(sseqEntry, function(senseIndex, senseEntry) {
                                            if (senseEntry[0] === "sense") {
                                                var sense = senseEntry[1];
                                                var cleanedDef = sense.dt && sense.dt[0] && typeof sense.dt[0][1] === 'string' ? cleanText(sense.dt[0][1]) : '';
                                                if (cleanedDef) {
                                                    results += '<li class="lead"><span style="font-size: 1em; font-weight: bold;">' + cleanedDef.replace(/{bc}/g, ' ').replace(/{sx\|([^|]+)\|([^}]+)}/g, '<i>$1</i>') + '</span><br>';

                                                    // Add synonyms
                                                    if (sense.syn_list && sense.syn_list.length > 0) {
                                                        results += '<strong>Synonyms:</strong> ';
                                                        results += sense.syn_list[0].map(function(syn) {
                                                            return syn.wd;
                                                        }).join(", ") + '<br>';
                                                    }

                                                    // Add related words
                                                    if (sense.rel_list && sense.rel_list.length > 0) {
                                                        results += '<strong>Related Words:</strong> ';
                                                        results += sense.rel_list[0].map(function(rel) {
                                                            return rel.wd;
                                                        }).join(", ") + '<br>';
                                                    }

                                                    // Add near antonyms
                                                    if (sense.near_list && sense.near_list.length > 0) {
                                                        results += '<strong>Near Antonyms:</strong> ';
                                                        results += sense.near_list[0].map(function(near) {
                                                            return near.wd;
                                                        }).join(", ") + '<br>';
                                                    }

                                                    // Add antonyms
                                                    if (sense.ant_list && sense.ant_list.length > 0) {
                                                        results += '<strong>Antonyms:</strong> ';
                                                        results += sense.ant_list[0].map(function(ant) {
                                                            return ant.wd;
                                                        }).join(", ") + '<br>';
                                                    }

                                                    results += '</li>';
                                                }
                                            }
                                        });
                                    });
                                });

                                results += '</ol></div></div></div>';
                                hasExactMatches = true;
                            });
                            results += '</div>';
                        }
                    }
                }

                if (!hasExactMatches) {
                    if (suggestions.length > 0) {
                        results = '<div class="alert alert-warning mt-3">';
                        results += '<p>Did you mean this?</p>';
                        results += '<ul class="p-2" style="columns: 2;-webkit-columns: 2;-moz-columns: 2;">';

                        $.each(suggestions, function(index, suggestion) {
                            results += '<li><a role="button" class="suggestion" data-word="' + suggestion + '">' + suggestion + '</a></li>';
                        });

                        results += '</ul></div>';
                    } else {
                        results = '<div class="alert alert-warning mt-3">';
                        results += '<p class="m-0">Oops, the word you searched for is not in the dictionary.</p>';
                        results += '</div>';
                    }
                }

                $("#dictionary-results").html(results);
            },
            error: function(jqxhr, textStatus, error) {
                var err = textStatus + ", " + error;
                console.log("Request Failed: " + err);
                var errorMessage = '<div class="alert alert-danger mt-3">';
                errorMessage += '<p class="m-0">There was an error processing your request. Please check your internet connection and try again.</p>';
                errorMessage += '</div>';
                $("#dictionary-results").html(errorMessage);
            }
        });
    }

    function init() {
        function handleSearch() {
            var searchword = $("#searchword").val().trim();
            if (searchword === "") {
                $("#dictionary-results").html('<div class="alert alert-warning mt-3"><p class="m-0">Please type something.</p></div>');
            } else {
                performSearch(searchword);
            }
        }

        $(".dictionary-search-form").on("click", "#search-button", function(){
            handleSearch();
        });

        $(".dictionary-search-form").submit(function(event) {
            event.preventDefault();
            handleSearch();
        });

        $(".dictionary-search-form").on("keydown", "#searchword", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                handleSearch();
            }
        });

        $("#dictionary-results").on("click", ".suggestion", function(e) {
            e.preventDefault();
            var suggestion = $(this).attr("data-word");
            $("#searchword").val(suggestion);
            performSearch(suggestion);
        });
    }

    return {
        init: init
    };
});
