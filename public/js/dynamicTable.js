class DynamicTable
{

    constructor(container) {

        container = (container instanceof Object)?container: $(container);

        this.container = container;
        this.table = container.children().find('.dynamicTable-table')
        this.filters = container.find('.dynamicTable-filters')
        this.pagination = container.children().find('.dynamicTable-pagination')

        console.log([this.filters, this.filters.length]);
    }

    drawTable(data, current_page, total_pages)
    {
        this.table.find('tbody').remove()
        this.table.append('<tbody></tbody>');
        let tbody = this.table.find('tbody');

        //add records to table
        $.each(data, function(id, values){
            tbody.append(
                '<tr>' +
                '<td><input type="checkbox" class="items-checbox" data-item="'+values.id+'"></td>' +
                '<td>'+values.id+'</td>' +
                '<td>'+values.email+'</td>' +
                '<td>'+values.subscribed+'</td>' +
                '</tr>'
            );
        });

        this.drawTablePagination(current_page, total_pages);

    }

    drawTablePagination(page, total_pages)
    {
        let paginationContainer = this.pagination;
        paginationContainer.children().remove();

        if(paginationContainer.length <= 0) {
            return false;
        }

        if(total_pages <= 1) {
            return;
        }

        let pages_list = _.range((page-3), (page+3))

        $.each(pages_list, function(id, this_page){

            if(this_page <= 0 || this_page > total_pages)
                return true;

            let classValues = (this_page == page) ?'active':'';
            paginationContainer.append('<a href="#" class="'+classValues+'" data-page="'+this_page+'">'+this_page+'</a>');

        });

        if(page != 1) {
            paginationContainer.prepend('<a href="#" class="" data-page="1">First</a>');
        }

        if(page < total_pages) {
            paginationContainer.append('<a href="#" class="" data-page="'+total_pages+'">Last </a>');
        }
    }

    drawFilters(current_filter, filters_list){

        let filtersContainer = this.filters;
        filtersContainer.children().remove();

        $.each(filters_list, function(id, filter){

            let filter_class = (current_filter == filter)?'active':'';
            filtersContainer.append('<button class="filter-button '+filter_class+'" data-filter-name="'+filter+'">'+filter+'</button>');

        });
    }
}