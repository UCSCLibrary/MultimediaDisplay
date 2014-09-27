jQuery(function() {
    Mirador({
        "id": "viewer",
        "data": [
            {
                "manifestUri": manifestUri,
                "title": title,
                "widgets": [{
                    "height": height,
                    "openAt": openAt,
                    "type": "imageView",
                    "width": width
                }, {
                    "height":thumbHeight,
                    "type": "thumbnailsView",
                    "width": width
                }]
            },{ 
                "manifestUri": colManifestUri,
                "location": location, 
                "title": colTitle, 
                "widgets": [] 
            }
        ]
    });
});
