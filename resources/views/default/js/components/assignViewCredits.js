export default function (e = {}) {
    return {
        isShared: e.isShared ?? false,
        // sharedLimit: e.sharedLimit ?? 0,
        limits: e.limits ?? {},
        init() {
            // this.$watch('sharedLimit', value => this.applyToAll())
        },
        pushToAiEngines(engineData) {
            this.aiEngines.push(engineData)
        },
        // applyToAll() {
        //     if (!this.isShared) {
        //         this.limits.forEach((model) => {
        //             if (this.$refs[`limit-${model.slug}`]) {
        //                 this.$refs[`limit-${model.slug}`].value = this.sharedLimit;
        //             }
        //         });
        //     }
        // }
    }
}
