import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ContactController::index
 * @see app/Http/Controllers/ContactController.php:26
 * @route '/contacts'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/contacts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::index
 * @see app/Http/Controllers/ContactController.php:26
 * @route '/contacts'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::index
 * @see app/Http/Controllers/ContactController.php:26
 * @route '/contacts'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ContactController::index
 * @see app/Http/Controllers/ContactController.php:26
 * @route '/contacts'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ContactController::index
 * @see app/Http/Controllers/ContactController.php:26
 * @route '/contacts'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ContactController::index
 * @see app/Http/Controllers/ContactController.php:26
 * @route '/contacts'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ContactController::index
 * @see app/Http/Controllers/ContactController.php:26
 * @route '/contacts'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\ContactController::store
 * @see app/Http/Controllers/ContactController.php:58
 * @route '/contacts'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/contacts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContactController::store
 * @see app/Http/Controllers/ContactController.php:58
 * @route '/contacts'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::store
 * @see app/Http/Controllers/ContactController.php:58
 * @route '/contacts'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ContactController::store
 * @see app/Http/Controllers/ContactController.php:58
 * @route '/contacts'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ContactController::store
 * @see app/Http/Controllers/ContactController.php:58
 * @route '/contacts'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\ContactController::edit
 * @see app/Http/Controllers/ContactController.php:78
 * @route '/contacts/{contact}/edit'
 */
export const edit = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/contacts/{contact}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::edit
 * @see app/Http/Controllers/ContactController.php:78
 * @route '/contacts/{contact}/edit'
 */
edit.url = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contact: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { contact: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    contact: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        contact: typeof args.contact === 'object'
                ? args.contact.id
                : args.contact,
                }

    return edit.definition.url
            .replace('{contact}', parsedArgs.contact.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::edit
 * @see app/Http/Controllers/ContactController.php:78
 * @route '/contacts/{contact}/edit'
 */
edit.get = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ContactController::edit
 * @see app/Http/Controllers/ContactController.php:78
 * @route '/contacts/{contact}/edit'
 */
edit.head = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ContactController::edit
 * @see app/Http/Controllers/ContactController.php:78
 * @route '/contacts/{contact}/edit'
 */
    const editForm = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ContactController::edit
 * @see app/Http/Controllers/ContactController.php:78
 * @route '/contacts/{contact}/edit'
 */
        editForm.get = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ContactController::edit
 * @see app/Http/Controllers/ContactController.php:78
 * @route '/contacts/{contact}/edit'
 */
        editForm.head = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \App\Http\Controllers\ContactController::update
 * @see app/Http/Controllers/ContactController.php:120
 * @route '/contacts/{contact}'
 */
export const update = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/contacts/{contact}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\ContactController::update
 * @see app/Http/Controllers/ContactController.php:120
 * @route '/contacts/{contact}'
 */
update.url = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contact: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { contact: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    contact: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        contact: typeof args.contact === 'object'
                ? args.contact.id
                : args.contact,
                }

    return update.definition.url
            .replace('{contact}', parsedArgs.contact.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::update
 * @see app/Http/Controllers/ContactController.php:120
 * @route '/contacts/{contact}'
 */
update.put = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\ContactController::update
 * @see app/Http/Controllers/ContactController.php:120
 * @route '/contacts/{contact}'
 */
update.patch = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\ContactController::update
 * @see app/Http/Controllers/ContactController.php:120
 * @route '/contacts/{contact}'
 */
    const updateForm = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ContactController::update
 * @see app/Http/Controllers/ContactController.php:120
 * @route '/contacts/{contact}'
 */
        updateForm.put = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\ContactController::update
 * @see app/Http/Controllers/ContactController.php:120
 * @route '/contacts/{contact}'
 */
        updateForm.patch = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\ContactController::destroy
 * @see app/Http/Controllers/ContactController.php:137
 * @route '/contacts/{contact}'
 */
export const destroy = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/contacts/{contact}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ContactController::destroy
 * @see app/Http/Controllers/ContactController.php:137
 * @route '/contacts/{contact}'
 */
destroy.url = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contact: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { contact: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    contact: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        contact: typeof args.contact === 'object'
                ? args.contact.id
                : args.contact,
                }

    return destroy.definition.url
            .replace('{contact}', parsedArgs.contact.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::destroy
 * @see app/Http/Controllers/ContactController.php:137
 * @route '/contacts/{contact}'
 */
destroy.delete = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\ContactController::destroy
 * @see app/Http/Controllers/ContactController.php:137
 * @route '/contacts/{contact}'
 */
    const destroyForm = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ContactController::destroy
 * @see app/Http/Controllers/ContactController.php:137
 * @route '/contacts/{contact}'
 */
        destroyForm.delete = (args: { contact: string | { id: string } } | [contact: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\ContactController::templateCsv
 * @see app/Http/Controllers/ContactController.php:146
 * @route '/contacts/template-csv'
 */
export const templateCsv = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: templateCsv.url(options),
    method: 'get',
})

templateCsv.definition = {
    methods: ["get","head"],
    url: '/contacts/template-csv',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::templateCsv
 * @see app/Http/Controllers/ContactController.php:146
 * @route '/contacts/template-csv'
 */
templateCsv.url = (options?: RouteQueryOptions) => {
    return templateCsv.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::templateCsv
 * @see app/Http/Controllers/ContactController.php:146
 * @route '/contacts/template-csv'
 */
templateCsv.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: templateCsv.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ContactController::templateCsv
 * @see app/Http/Controllers/ContactController.php:146
 * @route '/contacts/template-csv'
 */
templateCsv.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: templateCsv.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ContactController::templateCsv
 * @see app/Http/Controllers/ContactController.php:146
 * @route '/contacts/template-csv'
 */
    const templateCsvForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: templateCsv.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ContactController::templateCsv
 * @see app/Http/Controllers/ContactController.php:146
 * @route '/contacts/template-csv'
 */
        templateCsvForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: templateCsv.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ContactController::templateCsv
 * @see app/Http/Controllers/ContactController.php:146
 * @route '/contacts/template-csv'
 */
        templateCsvForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: templateCsv.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    templateCsv.form = templateCsvForm
/**
* @see \App\Http\Controllers\ContactController::importCsv
 * @see app/Http/Controllers/ContactController.php:169
 * @route '/contacts/import-csv'
 */
export const importCsv = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importCsv.url(options),
    method: 'post',
})

importCsv.definition = {
    methods: ["post"],
    url: '/contacts/import-csv',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContactController::importCsv
 * @see app/Http/Controllers/ContactController.php:169
 * @route '/contacts/import-csv'
 */
importCsv.url = (options?: RouteQueryOptions) => {
    return importCsv.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::importCsv
 * @see app/Http/Controllers/ContactController.php:169
 * @route '/contacts/import-csv'
 */
importCsv.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importCsv.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ContactController::importCsv
 * @see app/Http/Controllers/ContactController.php:169
 * @route '/contacts/import-csv'
 */
    const importCsvForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: importCsv.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ContactController::importCsv
 * @see app/Http/Controllers/ContactController.php:169
 * @route '/contacts/import-csv'
 */
        importCsvForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: importCsv.url(options),
            method: 'post',
        })
    
    importCsv.form = importCsvForm
const contacts = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
templateCsv: Object.assign(templateCsv, templateCsv),
importCsv: Object.assign(importCsv, importCsv),
}

export default contacts